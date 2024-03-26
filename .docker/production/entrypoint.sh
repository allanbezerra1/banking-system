#!/bin/sh

set -e

php artisan storage:link
php artisan l5-swagger:generate
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --seed --force

# Configurar permissões
chmod 777 -R storage/
chmod 777 -R bootstrap/cache/

nginx
php-fpm

