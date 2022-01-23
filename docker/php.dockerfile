FROM php:7.4-fpm AS base

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
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
  && docker-php-ext-install zip

RUN apt-get install libonig-dev -y \
    && docker-php-ext-install mbstring

RUN docker-php-ext-install opcache



### DEV Stage
FROM base AS dev

# Install developer tools
RUN apt-get install git -y

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc && \
        composer

VOLUME ["/app", "/composer/home/cache"]

COPY ./docker/dev.opcache.ini /usr/local/etc/php/conf.d/opcache.ini


### PreProd Stage
FROM dev AS dev_stage

COPY ./bin /app/bin
COPY ./config /app/config
COPY ./public/index.php /app/public/index.php
COPY ./public/build /app/public/build
COPY ./src /app/src
COPY ./templates /app/templates
COPY ./translations /app/translations
COPY ./vendor /app/vendor
COPY ./.env /app/.env
# для работы страницы about
COPY ./README.md /app/README.md

RUN mkdir -p /app/var/log && mkdir -p /app/var/cache
VOLUME ["/app/var/log", "/app/var/cache"]


### PROD Stage
FROM base AS prod_stage

COPY ./docker/prod.opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY ./bin /app/bin
# для работы страницы about
COPY ./README.md /app/README.md
COPY ./vendor /app/vendor
COPY ./.env /app/.env
COPY ./config /app/config
COPY ./translations /app/translations
COPY ./public/index.php /app/public/index.php
COPY ./templates /app/templates
COPY ./public/build /app/public/build
COPY ./src /app/src

RUN mkdir -p /app/var/cache

VOLUME ["/app/var/cache"]

RUN chmod 775 /app/var/cache