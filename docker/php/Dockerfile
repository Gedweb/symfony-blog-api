FROM php:8.3-fpm-bookworm

RUN apt-get -qq update \
    && apt-get -qq install git p7zip unzip

RUN docker-php-ext-install pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY dev/xdebug.ini $PHP_INI_DIR/conf.d//xdebug.ini

WORKDIR /application
