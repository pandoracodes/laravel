#!/bin/sh
set -e

# public/ ikut ter-bake di image (read-only saat runtime karena
# read_only: true di compose). nginx butuh akses ke file statisnya,
# jadi kita sync ke volume terpisah yang di-mount juga oleh nginx.
if [ -d /shared/public ]; then
  rsync -a --delete /var/www/public/ /shared/public/
fi

# Aman dijalankan berkali-kali tiap start; hanya butuh env var runtime
# (dari env_file), tidak butuh apa pun yang di-bake saat build.
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "Menjalankan migration..."
  php artisan migrate --force
fi

exec "$@"
