FROM php:8.1-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN set -xe \
    && apt-get update  \
    && apt-get install -y \
        build-essential \
        libgearman-dev \
        zip \
        libzip-dev \
    # xdebug
        && pecl install xdebug-3.2.1 \
        && docker-php-ext-enable xdebug \
    # gearman
        && pecl install gearman \
        && docker-php-ext-enable gearman \
    # pcntl
        && docker-php-ext-configure pcntl --enable-pcntl \
        && docker-php-ext-install pcntl \
    # other
        && docker-php-ext-install zip \
