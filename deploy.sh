#!/bin/bash

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

APP_DIR="/home/u722035022/domains/driving.mkscholars.com/laravel"
PUBLIC_DIR="/home/u722035022/domains/driving.mkscholars.com/public_html"

echo -e "${YELLOW}🚀 Starting deployment...${NC}"

cd $APP_DIR

# Ensure required directories
echo -e "${YELLOW}📂 Creating required directories...${NC}"
mkdir -p bootstrap/cache
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions

# Set proper permissions
echo -e "${YELLOW}🔒 Setting permissions...${NC}"
# Set directory permissions to 775
find storage -type d -exec chmod 775 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
# Set file permissions to 664
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;

# PHP dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction

# Storage link - with detailed error handling
if [ ! -L "public/storage" ]; then
    echo -e "${YELLOW}🔗 Linking storage...${NC}"
    
    # Debug: Show current directory and permissions
    echo "📂 Current directory: $(pwd)"
    echo "🔍 Storage directory contents:"
    ls -la storage/ 2>/dev/null || echo "No storage directory found"
    
    # Ensure public directory exists
    mkdir -p public
    
    # Remove any existing symlink or file
    if [ -e "public/storage" ]; then
        echo "🗑️  Removing existing public/storage..."
        rm -rf public/storage
    fi
    
    # Create the storage directory structure with proper permissions
    echo "📁 Creating storage/app/public directory..."
    mkdir -p storage/app/public
    chmod -R 775 storage/app/public
    
    # Create the symlink using PHP's native symlink function
    echo "🔗 Creating symlink..."
    if php -r "
        \$target = '$APP_DIR/storage/app/public';
        \$link = 'public/storage';
        
        echo "Creating symlink from: " . \$target . PHP_EOL;
        echo "To: " . \$link . PHP_EOL;
        
        if (!file_exists(\$target)) {
            echo "❌ Error: Target directory does not exist: " . \$target . PHP_EOL;
            exit(1);
        }
        
        if (file_exists(\$link)) {
            echo "❌ Error: Link already exists: " . \$link . PHP_EOL;
            exit(1);
        }
        
        if (!@symlink(\$target, \$link)) {
            echo "❌ Error: Failed to create symlink" . PHP_EOL;
            echo "Error: " . error_get_last()['message'] . PHP_EOL;
            exit(1);
        }
        
        echo "✅ Symlink created successfully" . PHP_EOL;
    "; then
        echo -e "${GREEN}✅ Storage linked successfully!${NC}"
    else
        echo -e "${RED}❌ Failed to create storage link. Trying alternative method...${NC}"
        
        # Fallback: Try creating a relative symlink
        echo "🔄 Trying alternative method with relative path..."
        if ln -sfn ../storage/app/public public/storage; then
            echo -e "${GREEN}✅ Storage linked successfully using fallback method!${NC}"
        else
            echo -e "${RED}❌ All storage link methods failed.${NC}"
            echo "💡 Please create the storage link manually by running:"
            echo "   ssh -p 65002 u722035022@82.29.189.212 'cd $APP_DIR && php artisan storage:link'"
            exit 1
        fi
    fi
    
    # Verify the symlink was created
    if [ -L "public/storage" ]; then
        echo -e "${GREEN}✅ Verified storage link exists${NC}"
    else
        echo -e "${YELLOW}⚠️  Warning: Storage link verification failed, but continuing...${NC}"
    fi
fi

# Database migrations
echo -e "${YELLOW}🔄 Running migrations...${NC}"
php artisan migrate --force

# Cache & optimize
echo -e "${YELLOW}🧹 Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Sync public folder **without index.php**
echo -e "${YELLOW}🔄 Syncing public directory...${NC}"
rsync -a --delete --exclude='index.php' public/ $PUBLIC_DIR/

# Overwrite index.php to point to laravel folder
echo -e "${YELLOW}✏️ Generating index.php...${NC}"
cat > $PUBLIC_DIR/index.php << 'EOF'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$app->handleRequest(Request::capture());
EOF

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
