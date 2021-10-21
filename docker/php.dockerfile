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

COPY ./bin /app/
COPY ./config /app/
COPY ./src /app/
COPY ./templates /app/
COPY ./translations /app/
COPY ./vendor /app/

VOLUME ["/app/var/log", "/app/var/cache"]


### PROD Stage
FROM base AS prod_stage

COPY ./docker/prod.opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY ./config /app/
COPY ./src /app/
COPY ./templates /app/
COPY ./translations /app/
COPY ./vendor /app/

RUN ln -s /dev/stdout /app/var/log/prod.log
VOLUME ["/app/var/cache"]