# MK Driving School - Hostinger Deployment Guide

This guide will walk you through deploying the MK Driving School application to Hostinger Premium Web Hosting with the correct folder structure.

## Prerequisites

1. Hostinger Premium Web Hosting account
2. Domain name pointed to your Hostinger hosting
3. SSH access enabled in your Hostinger control panel
4. PHP 8.1 or higher
5. Composer installed on your local machine
6. Node.js and NPM (for asset compilation)

## Important Note on Folder Structure

Hostinger requires a specific folder structure for Laravel applications to work correctly. The structure should be:

```
/home/u722035022/domains/driving.mkscholars.com/
    laravel/             # Your full Laravel app (root of repo)
        app/
        bootstrap/
        config/
        database/
        public/          # This will be symlinked to public_html
            index.php
            .htaccess
            ...
        resources/
        routes/
        storage/
        vendor/
    public_html/         # This will be a symlink to laravel/public/
```

## Step 1: Prepare Your Local Environment

1. Ensure all changes are committed to your Git repository
2. Run tests to make sure everything works locally
3. Update `.env.production` with your production values
4. Ensure your local repository has the correct folder structure (as shown above)

## Step 2: Set Up Hostinger Hosting

1. Log in to your Hostinger hPanel
2. Go to "Hosting" and select your hosting plan
3. Under "Files", open "File Manager"
4. Create a new directory called `laravel` in your root directory
5. Create a new database:
   - Go to "Databases" → "MySQL Databases"
   - Create a new database and user
   - Note down the database name, username, and password

## Step 3: Upload Your Application

### Using Git (Recommended)

1. In Hostinger hPanel, go to "Git"
2. Add a new repository
3. Connect your GitHub/GitLab account or add your repository URL
4. Set the deployment path to `/home/u722035022/domains/driving.mkscholars.com/laravel`
5. Enable auto-deploy on push

### Manual Upload (Alternative)

1. On your local machine, run:
   ```bash
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   
   # Build assets for production
   npm install
   npm run build
   
   # Clear and cache configuration
   php artisan config:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

2. Upload all files (except node_modules) to the `laravel` directory on your Hostinger hosting

## Step 4: Set Up the Public Directory

1. Connect to your Hostinger hosting via SSH or use the File Manager
2. Navigate to the root directory: `/home/u722035022/domains/driving.mkscholars.com/`
3. Create a symlink from `public_html` to `laravel/public`:
   ```bash
   # Remove existing public_html if it exists
   rm -rf public_html
   
   # Create symlink
   ln -s laravel/public public_html
   ```

## Step 5: Configure Environment

1. In Hostinger File Manager, navigate to `laravel`
2. Upload your `.env` file with production settings
3. Set proper permissions:
   ```bash
   # Set proper permissions
   chmod -R 755 laravel/storage
   chmod -R 755 laravel/bootstrap/cache
   
   # Set ownership (replace username with your Hostinger username)
   chown -R u722035022:u722035022 laravel/
   ```

## Step 6: Run Migrations and Optimize

1. SSH into your Hostinger hosting
2. Navigate to your Laravel directory:
   ```bash
   cd /home/u722035022/domains/driving.mkscholars.com/laravel
   ```
3. Run database migrations and seed if needed:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force  # If you have seeders
   ```
4. Generate application key if not set:
   ```bash
   php artisan key:generate
   ```
5. Create storage link:
   ```bash
   php artisan storage:link
   ```
6. Clear all caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```
7. Optimize the application:
   ```bash
   php artisan optimize
   ```

## Step 7: Set Up the Web Server

1. In Hostinger hPanel, go to "Domains" → "Manage"
2. Find your domain and click "Manage"
3. Under "Document Root", set it to:
   ```
   /home/u722035022/domains/driving.mkscholars.com/laravel/public
   ```
4. Save the changes

## Step 8: Verify the Installation

1. Visit your domain in a web browser
2. The application should now be accessible
3. Check the storage and cache directories are writable if you see any permission errors

## Troubleshooting

### Common Issues:

1. **403 Forbidden Error**:
   - Check file permissions
   - Ensure the `.htaccess` file exists in the public directory
   - Verify the document root is set correctly

2. **500 Server Error**:
   - Check the Laravel log: `laravel/storage/logs/laravel.log`
   - Ensure all environment variables are set correctly in `.env`
   - Verify database connection details

3. **Missing Vendor Files**:
   - Run `composer install` in the laravel directory
   - Ensure `vendor` directory is uploaded

4. **Asset Loading Issues**:
   - Run `npm run build`
   - Check the `public/build` directory exists
   - Verify asset URLs in your views

## Maintenance

To put the application in maintenance mode:
```bash
php artisan down
```

To bring it back up:
```bash
php artisan up
```

## Updating the Application

1. Pull the latest changes from your Git repository
2. Run:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   php artisan migrate --force
   php artisan optimize
   ```
3. Clear caches if needed
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
