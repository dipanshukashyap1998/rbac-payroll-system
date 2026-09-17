#!/bin/sh
set -e

echo "Waiting for database connection..."
until mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -e "SELECT 1;" > /dev/null 2>&1; do
  sleep 2
done

echo "Database connected!"

# Execute main process (PHP-FPM / Nginx)
exec "$@"
