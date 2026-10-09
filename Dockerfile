# syntax=docker/dockerfile:1

# ---- Dependensi Composer (produksi) ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts

# ---- PHP-FPM (aplikasi) ----
FROM php:8.4-fpm-alpine AS app

RUN apk add --no-cache fcgi \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql opcache

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

WORKDIR /var/www/html
COPY --chown=www-data:www-data --from=vendor /app /var/www/html
RUN rm -rf tests .env \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data
ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm"]

# ---- Nginx (melayani berkas statis, meneruskan PHP ke app) ----
FROM nginx:1.27-alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=vendor /app/public /var/www/html/public
