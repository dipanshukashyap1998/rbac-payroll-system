# 1. Base Image with PHP 8.2 FPM
FROM php:8.2-fpm

# 2. Set Working Directory FIRST (Fixes "Could not open input file: artisan")
WORKDIR /var/www/html

# 3. Install System Dependencies & Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# 4. Install PHP Extensions via Official Extension Installer Helper
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo pdo_mysql mbstring gd bcmath zip pcntl opcache

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy Application Source Code
COPY . /var/www/html

# 7. Install PHP Composer Dependencies
RUN composer install --no-dev --optimize-autoloader

# 8. Ensure Storage and Cache Directories Exist & Set Proper Permissions
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Expose Port
EXPOSE 8080

# 10. Startup Command
CMD ["sh", "./docker-entrypoint.sh"]
