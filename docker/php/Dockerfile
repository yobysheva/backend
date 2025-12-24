FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    libpq-dev \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
