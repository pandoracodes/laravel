# syntax=docker/dockerfile:1
#
# Dockerfile generik untuk semua app Laravel di proyek ini.
# Bedakan app lewat build-arg APP_PATH, contoh:
#   docker build --build-arg APP_PATH=apps/ampr -f docker/php/Dockerfile .
#   docker build --build-arg APP_PATH=apps/app2 -f docker/php/Dockerfile .
#
# Build context HARUS root repo (bukan folder app), karena butuh akses ke
# docker/php/* dan apps/<nama>/src sekaligus.

ARG APP_PATH=apps/ampr

# ---------- Stage 1: composer dependencies ----------
FROM composer:2 AS vendor
ARG APP_PATH
WORKDIR /app
COPY ${APP_PATH}/src/composer.json ${APP_PATH}/src/composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction
COPY ${APP_PATH}/src/ ./
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && rm -rf bootstrap/cache/*.php storage/logs/*.log

# ---------- Stage 2: frontend assets (vite/tailwind) ----------
FROM node:22-alpine AS frontend
ARG APP_PATH
WORKDIR /app
COPY ${APP_PATH}/src/package.json ${APP_PATH}/src/package-lock.json ./
RUN npm ci
COPY ${APP_PATH}/src/ ./
RUN npm run build

# ---------- Stage 3: runtime ----------
FROM php:8.4-fpm-alpine AS runtime
ARG APP_PATH

COPY --from=mlocati/docker-php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        oniguruma \
        libxml2 \
        tzdata \
        rsync \
    && install-php-extensions \
        pdo_mysql \
        mysqli \
        bcmath \
        exif \
        intl \
        gd \
        zip \
        opcache \
        redis \
    && rm -rf /tmp/* /var/cache/apk/*

ENV TZ=Asia/Jakarta

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

# Salin source dulu (tanpa vendor/node_modules, lihat .dockerignore),
# baru overlay vendor & build assets di atasnya.
COPY ${APP_PATH}/src/ .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# storage:link harus dibuat sekarang (build time), karena saat runtime
# public/ read-only (bukan volume) sehingga tidak bisa bikin symlink baru.
RUN php artisan storage:link --force || true

# Kunci kepemilikan & permission: hanya storage/ dan bootstrap/cache/
# yang butuh tulis, sisanya read-only meski root FS container writable.
RUN chown -R www-data:www-data /var/www \
    && find /var/www -type d -exec chmod 550 {} \; \
    && find /var/www -type f -exec chmod 440 {} \; \
    && chmod -R 770 /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
