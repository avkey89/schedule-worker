FROM php:7.4-fpm-alpine

RUN apk add --no-cache autoconf g++ make

RUN apk add curl

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer