#!/bin/sh

# Configurar permissões
chmod 777 -R storage/
chmod 777 -R bootstrap/cache/

# Iniciar o PHP-FPM
php-fpm
