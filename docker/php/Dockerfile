FROM php:8.4-fpm-bookworm

ARG WWWGROUP=1000
ARG WWWUSER=1000

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ${PHPIZE_DEPS} \
        git \
        unzip \
        libcurl4-openssl-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-source extract \
    && mkdir -p /usr/local/include/php/ext/dom \
    && cp /usr/src/php/ext/dom/dom_ce.h /usr/src/php/ext/dom/xml_common.h /usr/local/include/php/ext/dom/ \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install dom \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        posix \
        simplexml \
        xml \
        xmlreader \
        xmlwriter \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-source delete \
    && apt-get purge -y --auto-remove ${PHPIZE_DEPS} \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/conf.d/app.ini /usr/local/etc/php/conf.d/99-app.ini

RUN groupadd -g "${WWWGROUP}" app \
    && useradd -m -u "${WWWUSER}" -g "${WWWGROUP}" -s /bin/bash app

USER app
