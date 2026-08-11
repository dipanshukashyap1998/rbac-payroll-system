#!/bin/sh

# Start PHP-FPM in background
php-fpm -D

# Copy Nginx config and start Nginx
cp nginx.conf /etc/nginx/sites-available/default
service nginx start

# Run Laravel setup commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Keep container alive
tail -f /dev/null
