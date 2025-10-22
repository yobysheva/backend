FROM php:8.2-fpm

# Установка расширений Symfony
RUN apt-get update && apt-get install -y \
        libpq-dev \
        unzip \
        git \
        zip \
    && docker-php-ext-install pdo pdo_pgsql

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
