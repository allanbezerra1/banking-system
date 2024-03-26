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

if [ "${CONTAINER_TYPE}" = "app" ]; then
    nginx
    php-fpm
elif [ "${CONTAINER_TYPE}" = "worker" ]; then
    php artisan horizon
elif [ "${CONTAINER_TYPE}" = "schedule" ]; then
    php artisan schedule:work
else
    echo "O tipo do contêiner não foi especificado"
    exit 1
fi
