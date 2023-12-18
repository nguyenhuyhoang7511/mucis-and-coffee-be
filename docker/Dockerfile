# base
FROM php:8.1.13-fpm-alpine3.17 AS base

RUN apk add --update --no-cache bash python3 git supervisor mysql-client \
        libzip-dev \
        zip \
    && docker-php-ext-install zip

ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/1.5.33/install-php-extensions /usr/local/bin/

ENV IPE_GD_WITHOUTAVIF=1

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    @composer-2.3.5 \
    pdo_mysql \
    gd \
    xdebug-3.1.5 \
    imagick

COPY ./docker/config/ /var/config/

RUN cp /var/config/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    cp /var/config/php.ini /usr/local/etc/php/conf.d/php.ini-development.ini && \
    cp /var/config/supervisord.conf /etc/supervisord.conf && \
    mkdir -p /etc/supervisor/conf.d && \
    cp /var/config/laravel-worker.conf /etc/supervisor/conf.d/

COPY ./docker/config/crontab /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab

ENV PATH=/var/www/html/vendor/bin:$PATH

# build
FROM base

ENV XDEBUG_MODE=off

COPY --chown=www-data:www-data composer.json composer.lock ./

COPY ./docker/config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

RUN composer install --no-autoloader -n --no-scripts --no-dev

COPY --chown=www-data:www-data . .

RUN composer dump-autoload -o -n

CMD ["bash", "./docker/scripts/deploy-entrypoint.sh"]
