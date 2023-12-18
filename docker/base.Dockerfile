FROM php:8.1.13-fpm-alpine3.17

RUN apk add --update --no-cache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/1.5.33/install-php-extensions /usr/local/bin/

ENV IPE_GD_WITHOUTAVIF=1

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    @composer-2.3.5 \
    pdo_mysql \
    gd \
    xdebug-3.1.5 \
    imagick
