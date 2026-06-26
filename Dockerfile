FROM composer:2.10.1 AS composer
# set temporary working directory
WORKDIR /tmp
# copy composer manifest files
COPY composer.json composer.lock ./
# install production dependencies
RUN composer install --ignore-platform-reqs --no-dev --no-plugins --no-scripts

FROM ghcr.io/php/pie:bin AS pie

FROM php:8.5.7-fpm-alpine AS php
# set temporary working directory
WORKDIR /opt/project
# copy pie binary for PHP extension installation
COPY --from=pie /pie /usr/local/bin/pie
# install system packages, build dependencies, extensions, and update certificates
RUN set -eux \
    && apk add --no-cache ca-certificates curl git nginx p7zip runuser supervisor unzip \
    && apk add --no-cache freetype icu libjpeg-turbo libpng libpq libxml2 libxslt libzip rabbitmq-c zlib \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS freetype-dev icu-dev libjpeg-turbo-dev libtool \
        libpng-dev libpq-dev libxml2-dev libxslt-dev libzip-dev linux-headers rabbitmq-c-dev zlib-dev \
    && pie install --no-cache --skip-enable-extension php-amqp/php-amqp:2.2.0 \
    && pie install --no-cache --skip-enable-extension apcu/apcu:5.1.28 \
    && pie install --no-cache --skip-enable-extension phpredis/phpredis:6.3.0 \
    && pie install --no-cache --skip-enable-extension xdebug/xdebug:3.5.3 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd intl pcntl pdo_pgsql xsl zip \
    && docker-php-ext-enable amqp apcu redis \
    && update-ca-certificates --fresh \
    && apk del .build-deps \
    && rm -rf /tmp/* /usr/local/lib/php/doc/*
# copy configuration files for services and runtime
COPY ./config/docker/messenger.conf /etc/supervisor/messenger.conf
COPY ./config/docker/nginx.conf /etc/nginx/nginx.conf
COPY ./config/docker/php.conf /usr/local/etc/php/php.ini
COPY ./config/docker/supervisor.conf /etc/supervisor/supervisord.conf
COPY ./config/docker/www.conf /usr/local/etc/php-fpm.conf
# copy composer keys, binary, and vendor dependencies
COPY --from=composer /tmp/keys.dev.pub /root/.composer/keys.dev.pub
COPY --from=composer /tmp/keys.tags.pub /root/.composer/keys.tags.pub
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /tmp/vendor ./vendor
COPY . .
# configure git, create dirs, set permissions, and dump composer autoload
RUN git config --global --add safe.directory "$PWD" \
    && mkdir -p "$PWD/public/bundles" "$PWD/var" \
    && chown -R www-data:www-data "$PWD" \
    && runuser -u www-data -- composer dump-autoload --classmap-authoritative \
    && runuser -u www-data -- composer dump-env prod --empty
# expose ports
EXPOSE 80 9000
# healthcheck for service availability
HEALTHCHECK --interval=2s --timeout=5s --retries=1 CMD curl -f http://localhost/api/health || exit 1
# set container entrypoint
ENTRYPOINT ["/bin/sh", "./config/docker/entrypoint.conf"]
