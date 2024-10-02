FROM composer:2.7.9 AS composer
# set working directory
WORKDIR /tmp
# copy composer files
COPY composer.json composer.lock ./
# validate composer files syntax and perform automated checks
RUN composer validate --strict && composer diagnose
# install composer dependencies
RUN composer install --ignore-platform-reqs --no-cache --no-dev --no-plugins --no-scripts

FROM php:8.3.12-fpm-alpine AS php
# set working directory
WORKDIR /opt/project
# install system packages
RUN set -e \
    && apk add --update ca-certificates git linux-headers nginx p7zip runuser supervisor unzip \
    && apk add --update icu-dev libpng-dev libpq-dev libxml2-dev libxslt-dev libzip-dev rabbitmq-c-dev zlib-dev \
    && apk add --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install gd intl opcache pcntl pdo_pgsql xsl zip \
    && pecl install amqp-2.1.2 && docker-php-ext-enable amqp \
    && pecl install apcu-5.1.23 && docker-php-ext-enable apcu \
    && pecl install redis-6.0.2 && docker-php-ext-enable redis \
    && pecl install xdebug-3.3.2 && docker-php-ext-enable xsl \
    && pecl clear-cache && apk del .build-deps \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apk/*
# copy configuration files
COPY ./config/docker/messenger.conf /etc/supervisor/messenger.conf
COPY ./config/docker/nginx.conf /etc/nginx/nginx.conf
COPY ./config/docker/php.conf /usr/local/etc/php/php.ini
COPY ./config/docker/supervisor.conf /etc/supervisor/supervisord.conf
COPY ./config/docker/www.conf /usr/local/etc/php-fpm.conf
# copy composer and source files
COPY --from=composer /tmp/keys.dev.pub /root/.composer/keys.dev.pub
COPY --from=composer /tmp/keys.tags.pub /root/.composer/keys.tags.pub
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /tmp/vendor ./vendor
COPY . .
# create directories and change system rights
RUN mkdir -p $PWD/public/bundles $PWD/var && chown -R www-data:www-data $PWD
# clear environment variables and dump autoload
RUN runuser -u www-data -- composer dump-autoload --classmap-authoritative
RUN runuser -u www-data -- composer dump-env prod --empty
# expose web server and php-fpm port
EXPOSE 80 9000
# set healthcheck
HEALTHCHECK --interval=2s --timeout=5s --retries=1 \
    CMD curl -f http://localhost/api/health || exit 1
# set entrypoint
ENTRYPOINT ["/bin/sh", "./config/docker/entrypoint.conf"]
