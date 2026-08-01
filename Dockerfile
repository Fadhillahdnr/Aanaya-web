# syntax=docker/dockerfile:1

# =========================================================
# Frontend assets
# =========================================================
FROM node:22-alpine AS frontend

WORKDIR /build

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build

# =========================================================
# Laravel + PHP-FPM
# =========================================================
FROM php:8.2-fpm-bookworm AS app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libonig-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_pgsql \
        pgsql \
        zip \
    && pecl install redis-6.3.0 \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .
COPY --from=frontend /build/public/build ./public/build
COPY docker/php/production.ini /usr/local/etc/php/conf.d/99-aanaya.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-aanaya.conf

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
    && rm -f public/hot \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && if [ ! -e public/storage ]; then ln -s ../storage/app/public public/storage; fi \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm", "--nodaemonize"]

# =========================================================
# Nginx: only public assets and FastCGI routing
# =========================================================
FROM nginx:1.28-alpine AS nginx

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/public /var/www/public

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1/up || exit 1
