FROM php:7.4-fpm

WORKDIR /var/www

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
  && docker-php-ext-install zip

RUN apt-get install libonig-dev -y \
    && docker-php-ext-install mbstring

RUN docker-php-ext-install opcache

