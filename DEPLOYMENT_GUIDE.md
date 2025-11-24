# MK Driving School - Hostinger Deployment Guide

This guide will walk you through deploying the MK Driving School application to Hostinger Premium Web Hosting.

## Prerequisites

1. Hostinger Premium Web Hosting account
2. Domain name pointed to your Hostinger hosting
3. SSH access enabled in your Hostinger control panel
4. PHP 8.1 or higher
5. Composer installed on your local machine
6. Node.js and NPM (for asset compilation)

## Step 1: Prepare Your Local Environment

1. Ensure all changes are committed to your Git repository
2. Run tests to make sure everything works locally
3. Update `.env.production.example` with your production values and save as `.env`

## Step 2: Set Up Hostinger Hosting

1. Log in to your Hostinger hPanel
2. Go to "Hosting" and select your hosting plan
3. Under "Files", open "File Manager"
4. Delete all files in the `public_html` directory (backup any existing site first)
5. Create a new database:
   - Go to "Databases" → "MySQL Databases"
   - Create a new database and user
   - Note down the database name, username, and password

## Step 3: Upload Your Application

### Option A: Using Git (Recommended)

1. In Hostinger hPanel, go to "Git"
2. Add a new repository
3. Connect your GitHub/GitLab account or add your repository URL
4. Set the deployment path to `public_html`
5. Enable auto-deploy on push

### Option B: Manual Upload

1. On your local machine, run:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
2. Compile assets:
   ```bash
   npm install
   npm run build
   ```
3. Upload all files to `public_html` using FTP/SFTP or File Manager

## Step 4: Configure Environment

1. In Hostinger File Manager, navigate to `public_html`
2. Upload your `.env` file with production settings
3. Set the following permissions:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   chmod 644 .env
   ```

## Step 5: Set Up Database

1. Import your database:
   - Go to "Databases" → "MySQL Databases"
   - Click "Import" and upload your database dump
   - Or use the command line:
     ```bash
     mysql -u username -p database_name < database_dump.sql
     ```

2. Run migrations:
   ```bash
   php artisan migrate --force
   ```

## Step 6: Configure Web Server

1. In Hostinger hPanel, go to "Hosting" → "Manage"
2. Under "PHP Configuration", set:
   - PHP Version: 8.1 or higher
   - PHP Extensions: Enable `fileinfo`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`
3. Set the document root to `public_html/public`
4. Enable HTTPS with Let's Encrypt SSL

## Step 7: Set Up Cron Jobs

1. In Hostinger hPanel, go to "Cron Jobs"
2. Add a new cron job:
   ```
   * * * * * cd /home/u12345678/domains/yourdomain.com/public_html && php artisan schedule:run >> /dev/null 2>&1
   ```
   (Replace the path with your actual path)

## Step 8: Verify Installation

1. Visit your domain in a web browser
2. Check the following:
   - Homepage loads without errors
   - All routes work correctly
   - Assets (CSS/JS) load properly
   - Forms submit correctly
   - Emails are being sent

## Security Considerations

1. Ensure `.env` is not accessible from the web
2. Set proper file permissions:
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```
3. Enable Cloudflare (recommended) for additional security
4. Set up regular backups in Hostinger hPanel

## Troubleshooting

1. **500 Error**
   - Check `storage/logs/laravel.log` for errors
   - Ensure storage and bootstrap/cache are writable
   - Verify .env configuration

2. **Asset Loading Issues**
   - Run `php artisan storage:link`
   - Clear view cache: `php artisan view:clear`
   - Check file permissions

3. **Database Connection Issues**
   - Verify database credentials in .env
   - Check if database server is running
   - Ensure database user has proper permissions

## Maintenance

1. **Updates**
   - Pull latest changes from Git
   - Run `composer install`
   - Run migrations if needed: `php artisan migrate`
   - Clear caches

2. **Backups**
   - Set up automatic backups in Hostinger
   - Regularly export database dumps
   - Backup .env and other configuration files

## Support

For additional help, please contact [Your Support Email] or visit [Your Support Page].
