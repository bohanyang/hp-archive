FROM php:8.1.13-cli-alpine3.17 AS base

ARG PHP_EXT_INSTALLER_VERSION="1.5.47"
RUN set -eux; \
    curl -o /usr/local/bin/install-php-extensions -fsSL https://github.com/mlocati/docker-php-extension-installer/releases/download/${PHP_EXT_INSTALLER_VERSION}/install-php-extensions; \
    chmod a+rx /usr/local/bin/install-php-extensions; \
    install-php-extensions \
        apcu \
        bcmath \
        event \
        igbinary \
        intl \
        pcntl \
        redis \
        pdo_pgsql

FROM node:18.12.1-alpine3.17 AS assets-builder

COPY package.json yarn.lock /app/

RUN --mount=type=cache,target=/root/.yarn \
    --mount=type=cache,target=/app/node_modules \
    set -eux; \
    cd /app; \
    YARN_CACHE_FOLDER=/root/.yarn yarn

RUN --mount=type=bind,source=.,target=/usr/src/app \
    --mount=type=cache,target=/app/node_modules \
    set -eux; \
    cd /usr/src/app; \
    docker/copypath /app/ \
        assets \
        tsconfig.json \
        vite.config.js; \
    cd /app; \
    yarn build

FROM base AS builder

ARG S6_OVERLAY_VERSION="3.1.2.1"
RUN mkdir /overlay; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-noarch.tar.xz | tar -C /overlay -xJpf -; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-x86_64.tar.xz | tar -C /overlay -xJpf -

ARG COMPOSER_VERSION="2.4.4"
ARG COMPOSER_INSTALLER_VERSION="0a51b6fe383f7f61cf1d250c742ec655aa044c94"
RUN set -eux; \
    curl -fsSL "https://raw.githubusercontent.com/composer/getcomposer.org/${COMPOSER_INSTALLER_VERSION}/web/installer" | php -- --quiet --install-dir=/usr/local/bin --filename=composer --version="${COMPOSER_VERSION}"

ARG OFELIA_VERSION="0.3.6"
RUN set -eux; \
    curl -fsSL https://github.com/mcuadros/ofelia/releases/download/v0.3.6/ofelia-v0.3.6-linux-amd64.tar.gz | tar -C /usr/local/bin -xzf -

RUN --mount=type=bind,source=.,target=/usr/src/app \
    --mount=type=cache,target=/root/.composer \
    set -eux; \
    cd /usr/src/app; \
    docker/copypath /app/ \
        bin/console \
        config \
        lib/src \
        postgresql_migrations \
        public \
        src \
        templates \
        composer.json \
        composer.lock \
        symfony.lock \
        .env; \
    COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=prod \
    composer install --no-dev -d /app -o

FROM caddy:2.6.2-builder-alpine AS caddy-builder

RUN xcaddy build \
    --with github.com/abiosoft/caddy-yaml

FROM base

RUN apk add --no-cache \
    su-exec \
    # provides mime types for caddy
    mailcap \
    # for ofelia
    tzdata \
    # for ofelia
    libc6-compat

COPY --from=builder \
    /usr/local/bin/composer \
    /usr/local/bin/ofelia \
    /usr/local/bin/

COPY --from=caddy-builder /usr/bin/caddy /usr/local/bin/

COPY --from=builder /overlay /

COPY --from=builder --chown=www-data:www-data /app /app/
COPY --from=assets-builder --chown=www-data:www-data /app/public/build /app/public/build/

COPY docker/root /

ENV S6_CMD_WAIT_FOR_SERVICES_MAXTIME="60000" \
    S6_LOGGING_SCRIPT="n20 s1000000" \
    APP_RUNTIME_WORKERS="1"

ENTRYPOINT ["/init"]
