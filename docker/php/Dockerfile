ARG APP_PATH=apps/ampr

FROM composer:2 AS build
ARG APP_PATH
WORKDIR /app

COPY ${APP_PATH}/src/composer.json ${APP_PATH}/src/composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-reqs

COPY ${APP_PATH}/src/ ./
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && rm -rf bootstrap/cache/*.php storage/logs/*.log

RUN apk add --no-cache nodejs npm

RUN cp .env.example .env && php artisan key:generate --ansi

RUN npm ci --legacy-peer-deps
RUN npm run build

FROM php:8.4-fpm-alpine AS runtime
ARG APP_PATH

COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

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

COPY ${APP_PATH}/src/ .
COPY --from=build /app/vendor ./vendor
COPY --from=build /app/public/build ./public/build

RUN php artisan storage:link --force || true

RUN mkdir -p /shared/public && chown www-data:www-data /shared/public

RUN chown -R www-data:www-data /var/www \
    && find /var/www -type d -exec chmod 550 {} \; \
    && find /var/www -type f -exec chmod 440 {} \; \
    && chmod -R 770 /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
