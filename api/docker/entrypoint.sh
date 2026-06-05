#!/bin/sh
set -e

echo "Fixing Laravel writable directories sleep 2
done

if [ "${DB_FRESH_ON_START:-false}" = "true" ]; then
    echo "Running fresh migrations and seeders..."
    php artisan migrate:fresh --seed --force
else
    echo "Running migrations..."
    php artisan migrate --force

    echo "Running seeders..."
    php artisan db:seed --force
fi

echo "Starting PHP-FPM..."
exec "$@"