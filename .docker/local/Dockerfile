FROM php:8.3-fpm

# Instalar dependências do Ubuntu Linux
RUN apt update && \
    apt install -y libzip-dev $PHPIZE_DEPS

# Instalar pacotes necessarios para o MongoDB
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev

# Instalar extensões do PHP
RUN docker-php-ext-install opcache pcntl pdo_mysql zip
RUN pecl update-channels && \
    pecl install ds igbinary && \
    docker-php-ext-enable ds igbinary
RUN pecl install -D 'enable-redis-igbinary="yes"' redis && \
    docker-php-ext-enable redis

# Instalar extensoes do MongoDB
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

# Copiar configurações
COPY .docker/local/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY .docker/local/php/php-fpm.conf /usr/local/etc/php/conf.d/php-fpm.conf

# Instalar Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copiar projeto
COPY --chown=www-data:www-data . /app

WORKDIR /app

