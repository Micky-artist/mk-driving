#!/bin/bash

# Exit on error
set -e

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not exists
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Clear configuration cache
echo "Clearing configuration cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Link storage if not linked
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

echo "Deployment completed successfully!"
