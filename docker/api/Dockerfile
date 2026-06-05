# syntax=docker/dockerfile:1.7

ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-fpm-bookworm AS base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        git \
        libcurl4-openssl-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        dom \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        xml \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]

FROM base AS development

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist

COPY . .
RUN composer dump-autoload --classmap-authoritative --no-scripts \
    && php artisan package:discover --ansi

FROM base AS production

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache
