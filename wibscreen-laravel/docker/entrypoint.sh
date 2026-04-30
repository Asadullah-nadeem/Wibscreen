#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Run composer install if vendor folder is missing
if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if missing from .env
if ! grep -q "APP_KEY=base64:" .env || [ -z "$(grep "APP_KEY=" .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run migrations (with database check loop)
echo "Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1; do
  echo "Database is unavailable - sleeping"
  sleep 2
done

echo "Running migrations..."
php artisan migrate --force

# Execute the CMD from Dockerfile
exec "$@"
