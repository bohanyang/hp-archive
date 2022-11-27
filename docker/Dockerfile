FROM php:8.1.12-cli-bullseye AS base

ARG PHP_EXT_INSTALLER_VERSION="1.5.47"
RUN set -eux; \
    curl -o /usr/local/bin/install-php-extensions -fsSL https://github.com/mlocati/docker-php-extension-installer/releases/download/${PHP_EXT_INSTALLER_VERSION}/install-php-extensions; \
    chmod a+rx /usr/local/bin/install-php-extensions; \
    install-php-extensions \
        apcu \
        bcmath \
        bz2 \
        event \
        igbinary \
        intl \
        oci8 \
        pcntl \
        pdo_mysql \
        pdo_pgsql \
        redis \
        sockets \
        sysvsem \
        zip

FROM node:16-alpine AS frontend

COPY package.json yarn.lock /app/

RUN set -eux; \
    corepack enable; \
    cd /app; \
    yarn

COPY tsconfig.json vite.config.js /app/
COPY assets /app/assets/

RUN set -eux; \
    cd /app; \
    yarn build

FROM base AS builder

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        xz-utils \
        unzip

ARG S6_OVERLAY_VERSION="3.1.2.1"
RUN mkdir /overlay; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-noarch.tar.xz | tar -C /overlay -Jxpf -; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-x86_64.tar.xz | tar -C /overlay -Jxpf -

ARG COMPOSER_VERSION="2.4.4"
ARG COMPOSER_INSTALLER_VERSION="0a51b6fe383f7f61cf1d250c742ec655aa044c94"
RUN set -eux; \
    curl -fsSL "https://raw.githubusercontent.com/composer/getcomposer.org/${COMPOSER_INSTALLER_VERSION}/web/installer" | php -- --quiet --install-dir=/usr/local/bin --filename=composer --version="${COMPOSER_VERSION}"

ARG NGINX_VERSION="1.23.2"
RUN set -eux; \
    mkdir /nginx; \
    curl -fsSL https://github.com/manyou-io/nginx-build/releases/download/v${NGINX_VERSION}/nginx-${NGINX_VERSION}-bullseye_amd64.tar.xz | tar -C /nginx -Jxpf -

ENV \
    COMPOSER_ALLOW_SUPERUSER="1" \
    APP_ENV="prod"

COPY composer.json composer.lock symfony.lock /srv/app/
RUN set -eux; \
    composer install --no-cache --no-dev -d /srv/app --no-autoloader --no-scripts

COPY . /app/
RUN set -eux; \
    cd /app; \
    docker/copypath /srv/app/ \
        bin/console \
        config \
        lib/src \
        oci8_migrations \
        public \
        src \
        templates \
        .env; \
    composer install --no-cache --no-dev -d /srv/app -o

FROM base

COPY --from=builder /overlay /nginx /
COPY --from=builder --chown=www-data:www-data /srv/app /srv/app/
COPY --from=frontend --chown=www-data:www-data /app/public/build /srv/app/public/build/
COPY docker/root /

ENV \
    S6_CMD_WAIT_FOR_SERVICES_MAXTIME="60000" \
    APP_RUNTIME_WORKERS="1" 

ENTRYPOINT ["/init"]
