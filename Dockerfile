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

ENV PORT=80 \
    PHP_FPM_HOST=app \
    NGINX_ENVSUBST_FILTER='^(PORT|PHP_FPM_HOST)$'

COPY nginx.conf /etc/nginx/templates/default.conf.template
COPY --from=app /var/www/public /var/www/public

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1/up || exit 1

# =========================================================
# Render: Nginx + PHP-FPM in one public web service
# =========================================================
FROM app AS render

USER root

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        gettext-base \
        nginx-light \
        supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

COPY nginx.conf /etc/nginx/templates/aanaya.conf.template
COPY docker/render/supervisord.conf /etc/supervisor/conf.d/aanaya.conf
COPY docker/render/start.sh /usr/local/bin/start-aanaya-render

RUN chmod +x /usr/local/bin/start-aanaya-render

ENV PORT=10000 \
    PHP_FPM_HOST=127.0.0.1

EXPOSE 10000

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1:${PORT}/up > /dev/null || exit 1

CMD ["start-aanaya-render"]
