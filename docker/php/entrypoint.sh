#!/bin/sh
set -e

if [ -d /shared/public ]; then
  rsync -a --delete /var/www/public/ /shared/public/
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "Menjalankan migration..."
  php artisan migrate --force
fi

exec "$@"
