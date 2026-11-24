FROM composer:2.11.2@sha256:cd3a90dc5624c59a2f9ddf80dad08569f06416db31de519024f249d63bca554b AS composer
# set temporary working directory
WORKDIR /tmp
# copy composer manifest files
COPY composer.json composer.lock ./
# install production dependencies
RUN composer install --ignore-platform-reqs --no-autoloader --no-dev --no-interaction --no-plugins --no-scripts

FROM ghcr.io/php/pie:1.6.0-bin@sha256:f49c15dbb88a7aec373fdb15ab9004bf08958772db3b476c42364e10a8ea416f AS pie

FROM php:8.5.13-fpm-alpine@sha256:51414d5291758fe27f58a64c7e8a078465d32cc7dcef7bda803f5b36019802e0 AS php
# set default environment
ENV APP_ENV=prod PYTHONDONTWRITEBYTECODE=1
# set application working directory
WORKDIR /opt/project
# install packages and extensions, update certificates, configure git, create dirs, and set permissions
RUN --mount=type=bind,from=pie,source=/pie,target=/usr/local/bin/pie \
        apk add --no-cache ca-certificates curl freetype git icu libjpeg-turbo libpng libpq libxml2 libxslt libzip \
        nginx p7zip '!pyc' rabbitmq-c runuser supervisor unzip zlib \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS freetype-dev icu-dev libjpeg-turbo-dev \
        libpng-dev libpq-dev libtool libxml2-dev libxslt-dev libzip-dev linux-headers rabbitmq-c-dev zlib-dev \
    && pie install --no-cache --no-interaction --skip-enable-extension \
        apcu/apcu:5.1.28 php-amqp/php-amqp:2.2.0 phpredis/phpredis:6.3.0 xdebug/xdebug:3.5.3 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd intl pcntl pdo_pgsql xsl zip \
    && docker-php-ext-enable amqp apcu redis \
    && update-ca-certificates --fresh \
    && git config --system --add safe.directory "$PWD" \
    && mkdir -p "$PWD/public/bundles" "$PWD/var" /var/lib/nginx/tmp \
    && chown www-data:www-data "$PWD" "$PWD/public/bundles" "$PWD/var" \
    && chown www-data:www-data /var/lib/nginx /var/lib/nginx/tmp \
    && apk del .build-deps \
    && rm -rf /root/.pie /tmp/* /usr/local/lib/php/doc/* /var/cache/apk/*
# copy configuration files for services and runtime
COPY ./config/docker/messenger.conf /etc/supervisor/messenger.conf
COPY ./config/docker/nginx.conf /etc/nginx/nginx.conf
COPY ./config/docker/php.conf /usr/local/etc/php/php.ini
COPY ./config/docker/supervisor.conf /etc/supervisor/supervisord.conf
COPY ./config/docker/www.conf /usr/local/etc/php-fpm.conf
# copy composer keys and binary
COPY --from=composer /tmp/keys.dev.pub /root/.composer/keys.dev.pub
COPY --from=composer /tmp/keys.tags.pub /root/.composer/keys.tags.pub
COPY --from=composer /usr/bin/composer /usr/bin/composer
# copy dependencies and application code
COPY --from=composer --chown=www-data:www-data /tmp/vendor ./vendor
COPY --chown=www-data:www-data . .
# verify platform requirements, dump composer autoload and environment
RUN runuser -u www-data -- composer check-platform-reqs --no-dev \
    && runuser -u www-data -- composer dump-autoload --classmap-authoritative --no-dev \
    && runuser -u www-data -- composer dump-env --empty prod
# expose HTTP port
EXPOSE 80
# set container entrypoint
ENTRYPOINT ["./config/docker/entrypoint.conf"]
оно
