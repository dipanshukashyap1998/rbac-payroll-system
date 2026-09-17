# 1. Base Image with PHP 8.2 FPM
FROM php:8.2-fpm

# 2. Set Working Directory
WORKDIR /var/www/html

# 3. Install System Dependencies & Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nginx \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# 4. Install PHP Extensions
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo pdo_mysql mbstring gd bcmath zip pcntl opcache

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy Application Source Code
COPY . /var/www/html

# 7. Install PHP Composer Dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# 8. Setup Permissions and Move Entrypoint Script
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && cp /var/www/html/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Add this line right before EXPOSE 8080:
COPY nginx.conf /etc/nginx/sites-available/default

# 9. Expose Port
EXPOSE 8080

# 10. Configure Startup Execution
ENTRYPOINT ["docker-entrypoint.sh"]

# Starts Nginx in background, then starts PHP-FPM in foreground to keep container alive
CMD ["sh", "-c", "nginx && php-fpm"]
