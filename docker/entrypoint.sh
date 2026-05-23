#!/bin/bash
set -e

cd /var/www/html

mkdir -p storage/logs
mkdir -p bootstrap/cache

touch storage/logs/laravel.log

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan optimize:clear || true
php artisan config:clear || true
php artisan cache:clear || true

php artisan storage:link --force || true

exec "$@"