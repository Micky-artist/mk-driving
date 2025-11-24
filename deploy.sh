#!/bin/bash

# Exit on error and print commands
set -e

# Colors for better output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check for required commands
for cmd in php composer npm; do
    if ! command_exists "$cmd"; then
        echo -e "${RED}Error: $cmd is not installed${NC}"
        exit 1
    fi
done

echo -e "${YELLOW}🚀 Starting deployment...${NC}"

# Create required directories if they don't exist
echo -e "\n${YELLOW}📁 Creating required directories...${NC}"
mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} storage/logs
chmod -R 775 bootstrap/cache storage

# Install PHP dependencies
echo -e "\n${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build assets
if [ -f "package.json" ]; then
    echo -e "\n${YELLOW}🛠️  Installing Node.js dependencies...${NC}"
    npm install --no-audit --prefer-offline
    
    echo -e "\n${YELLOW}🔨 Building frontend assets...${NC}"
    npm run production
fi

# Set up environment
if [ ! -f ".env" ]; then
    echo -e "\n${YELLOW}🔑 Creating .env file...${NC}"
    cp .env.example .env
    php artisan key:generate
fi

# Link storage if not linked
if [ ! -L "public/storage" ]; then
    echo -e "\n${YELLOW}🔗 Linking storage...${NC}"
    php artisan storage:link
fi

# Set proper permissions
echo -e "\n${YELLOW}🔒 Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache

# Clear configuration cache
echo -e "\n${YELLOW}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
echo -e "\n${YELLOW}⚡ Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

Run database migrations (uncomment if needed)
echo -e "\n${YELLOW}🔄 Running database migrations...${NC}"
php artisan migrate --force

# Restart queue workers (uncomment if using queues)
# echo -e "\n${YELLOW}🔄 Restarting queue workers...${NC}"
# php artisan queue:restart

echo -e "\n${GREEN}✅ Deployment completed successfully!${NC}"