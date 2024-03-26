FROM php:8.3-fpm

# Instalar dependências do Ubuntu Linux
RUN apt update && \
    apt install -y nginx libzip-dev $PHPIZE_DEPS

# Instalar pacotes necessarios para o MongoDB
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev

# Instalar extensões do PHP
RUN docker-php-ext-install opcache pcntl pdo_mysql zip
RUN pecl update-channels && \
    pecl install ds igbinary && \
    docker-php-ext-enable ds igbinary

# Copiar configurações
COPY .docker/production/nginx/default.conf /etc/nginx/sites-available/default
COPY .docker/production/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY .docker/production/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY .docker/production/php/php-fpm.conf /usr/local/etc/php/conf.d/php-fpm.conf

# Instalar Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copiar projeto
COPY --chown=www-data:www-data . /app

WORKDIR /app

# Instalar dependências do projeto
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Determinar entrypoint
COPY .docker/production/entrypoint.sh /entrypoint.sh
ENTRYPOINT sh /entrypoint.sh

EXPOSE 80 9000
