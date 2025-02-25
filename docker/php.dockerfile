FROM php:8.2-fpm AS base
LABEL maintainer="Dmitry demius Vospennikov"
LABEL description="Task and Doc Tracker application backend part"

WORKDIR /app

# install GD
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# install MYSQL SQL client
RUN docker-php-ext-install pdo pdo_mysql

#install ICU INTL
RUN apt-get install -y libicu-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl

# install zip
RUN apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
  && docker-php-ext-install zip

RUN apt-get install libonig-dev -y \
    && docker-php-ext-install mbstring

RUN docker-php-ext-install opcache

RUN mkdir -p /app/var/log && mkdir -p /app/var/cache  \
    && chown www-data:www-data -R /app/var && chmod 775 -R /app/var


### DEV Stage
FROM base AS dev

# Install developer tools
RUN apt-get install git -y

# Install composer
RUN curl --silent --show-error "https://getcomposer.org/installer" | php -- --install-dir=/usr/local/bin --filename=composer


VOLUME ["/app", "/composer/home/cache"]

COPY ./docker/dev.php.ini /usr/local/etc/php/php.ini
COPY ./docker/dev.opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN chmod 550 -R /usr/local/etc/php


### PreProd Stage
FROM dev AS dev_stage

COPY ./bin /app/bin
COPY ./config /app/config
COPY ./docs /app/docs
COPY ./public/index.php /app/public/index.php
COPY ./public/build /app/public/build
COPY ./src /app/src
COPY ./templates /app/templates
COPY ./translations /app/translations
COPY ./vendor /app/vendor
COPY ./.env /app/.env
# для работы страницы about
COPY ./*.md /app/
COPY ./LICENSE /app/

VOLUME ["/app/var/log", "/app/var/cache"]


### PREPROD (RC,PERFOMANCE etc.) and PROD Stage
FROM base AS prod

COPY ./docker/prod.php.ini /usr/local/etc/php/php.ini
COPY ./docker/prod.opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN chmod 550 -R /usr/local/etc/php

### PROD Stage
FROM prod AS prod_stage

VOLUME ["/app/var/cache"]

COPY ./bin /app/bin
# для работы страницы about
COPY ./README.md /app/README.md
COPY ./CHANGELOG.md /app/CHANGELOG.md
COPY ./LICENSE /app/LICENSE
COPY ./vendor /app/vendor
COPY ./.env /app/.env
COPY ./config /app/config
COPY ./docs /app/docs
COPY ./translations /app/translations
COPY ./public/index.php /app/public/index.php
COPY ./templates /app/templates
COPY ./public/build /app/public/build
COPY ./src /app/src
