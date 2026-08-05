#!/bin/sh
set -eu

: "${PORT:=10000}"
: "${PHP_FPM_HOST:=127.0.0.1}"

export PORT PHP_FPM_HOST

envsubst '${PORT} ${PHP_FPM_HOST}' \
    < /etc/nginx/templates/aanaya.conf.template \
    > /etc/nginx/conf.d/aanaya.conf

php artisan storage:link --no-interaction >/dev/null 2>&1 || true

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
