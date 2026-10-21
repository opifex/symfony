FROM composer:2.10.3@sha256:af98f42dfff7c68ba8d53c2164fd9fde1087b7d449514baa38c418b1f6bc4bac AS composer
# set temporary working directory
WORKDIR /tmp
# copy composer manifest files
COPY composer.json composer.lock ./
# install production dependencies
RUN composer install --ignore-platform-reqs --no-autoloader --no-dev --no-interaction --no-plugins --no-scripts

FROM ghcr.io/php/pie:1.5.2-bin@sha256:a254c78b51ca34daef0541fa1cf0147385c93ba5ca799868392275d947f00fe7 AS pie

FROM php:8.5.12-fpm-alpine@sha256:4c9541a4506014672d074652a1cc9754d3c5f97cad6c6027bae35be0e4a4dea9 AS php
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
