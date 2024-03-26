# Use the official PHP image with PHP 8.3
FROM php:8.3-fpm as base

# Use the official Node.js image to build assets
FROM node:20 as node

# Install Composer in a separate stage to leverage Docker cache
FROM composer:latest as composer

# Start again from base image to reduce final image size
FROM base

# Install Composer globally
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy the application's package.json and package-lock.json to install dependencies
COPY package*.json /app/

# Install system dependencies and PHP extensions needed by Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    exif \
    nodejs \
    npm \
    apt-utils \
    # Clean up the apt cache to reduce image size
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    # Configure and install PHP extensions
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip pcntl \
    # Configure pcntl for better process control
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl \
    # Configure exif for better image handling
    && docker-php-ext-configure exif --enable-exif \
    && docker-php-ext-install exif

# Copy the application's composer.json and composer.lock to install dependencies
COPY composer.* /app/

# Copiar projeto
COPY --chown=www-data:www-data . /app

WORKDIR /app

# Install dependencies with Composer
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

RUN composer require laravel/octane

# Optimize the application
RUN php artisan optimize:clear
RUN php artisan optimize
    # Correct permissions for Laravel directories
RUN php artisan octane:install --server=frankenphp \
    # Correct permissions for Laravel directories
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Install dependencies with NPM
RUN npm install

# Build assets
RUN npm run dev

# Expose Octane port
EXPOSE 8000

# Start Octane with FrankenPHP
CMD ["php", "artisan", "octane:start", "--server=frankenphp"]
