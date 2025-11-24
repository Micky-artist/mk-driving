#!/bin/bash

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

APP_DIR="/home/u722035022/domains/driving.mkscholars.com/laravel"
PUBLIC_DIR="/home/u722035022/domains/driving.mkscholars.com/public_html"

echo -e "${YELLOW}🚀 Starting deployment...${NC}"

cd $APP_DIR

# PHP dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
echo -e "${YELLOW}🔒 Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache

# Storage link
if [ ! -L "public/storage" ]; then
    echo -e "${YELLOW}🔗 Linking storage...${NC}"
    php artisan storage:link
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
