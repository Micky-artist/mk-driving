#!/bin/bash

# Exit on error
set -e

# Colors for better output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🚀 Starting deployment...${NC}"

# Install PHP dependencies
echo -e "\n${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
echo -e "\n${YELLOW}🛠️  Installing Node.js dependencies...${NC}
npm install

echo -e "\n${YELLOW}🔨 Building frontend assets...${NC}
npm run build

# Generate application key if not exists
if [ ! -f ".env" ]; then
    echo -e "\n${YELLOW}🔑 Generating application key...${NC}"
    cp .env.example .env
    php artisan key:generate
fi

# Set proper permissions
echo -e "\n${YELLOW}🔒 Setting permissions...${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Clear configuration cache
echo -e "\n${YELLOW}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Optimize for production
echo -e "\n${YELLOW}⚡ Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Run migrations
echo -e "\n${YELLOW}🔄 Running migrations...${NC}"
php artisan migrate --force --no-interaction

# Link storage if not linked
if [ ! -L "public/storage" ]; then
    echo -e "\n${YELLOW}🔗 Creating storage link...${NC}"
    php artisan storage:link
fi

# Clear and cache routes and config
echo -e "\n${YELLOW}🔄 Caching routes and config...${NC}"
php artisan config:cache
php artisan route:cache

# Restart queue workers if using them
if [ -f "storage/logs/worker.log" ]; then
    echo -e "\n${YELLOW}🔄 Restarting queue workers...${NC}"
    php artisan queue:restart
fi

echo -e "\n${GREEN}✅ Deployment completed successfully!${NC}"
