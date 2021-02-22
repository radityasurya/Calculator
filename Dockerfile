FROM php:7.4

LABEL maintainer="Raditya Surya <radityasurya@hotmail.com>"

RUN apt-get update          \
    && apt-get install -y   \
        git                 \
        zip                 \
        unzip               \
        libzip-dev          \
    && pecl install xdebug  \
    && docker-php-ext-install zip \
    && docker-php-ext-enable xdebug \
    && apt-get clean all \
    && rm -rvf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/bin
ENV PATH /root/.composer/vendor/bin:$PATH