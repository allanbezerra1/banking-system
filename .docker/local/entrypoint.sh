#!/bin/sh

set -e

php artisan storage:link
php artisan optimize:clear
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# clean database
php artisan migrate:fresh --seed --force

# Configurar permissões
chmod 777 -R storage/
chmod 777 -R bootstrap/cache/

# Iniciar o PHP-FPM
php-fpm
