#!/bin/sh
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

if [ "$FILESYSTEM_DISK" = "public" ]; then
    php artisan storage:link || true
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
