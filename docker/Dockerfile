FROM php:8.2-cli-alpine AS base

ARG PHP_EXT_INSTALLER_VERSION="2.1.23"
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

FROM node:18-alpine AS assets-builder

COPY package.json yarn.lock /app/

RUN --mount=type=cache,target=/root/.yarn \
    set -eux; \
    cd /app; \
    YARN_CACHE_FOLDER=/root/.yarn yarn

RUN --mount=type=bind,source=.,target=/usr/src/app \
    set -eux; \
    cd /usr/src/app; \
    docker/copypath /app/ \
        assets \
        tsconfig.json \
        vite.config.js; \
    cd /app; \
    yarn build

FROM base AS builder

ARG S6_OVERLAY_VERSION="3.1.5.0"
RUN mkdir /overlay; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-noarch.tar.xz | tar -C /overlay -xJpf -; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-x86_64.tar.xz | tar -C /overlay -xJpf -

ARG COMPOSER_VERSION="2.5.5"
ARG COMPOSER_INSTALLER_VERSION="0a51b6fe383f7f61cf1d250c742ec655aa044c94"
RUN set -eux; \
    curl -fsSL "https://raw.githubusercontent.com/composer/getcomposer.org/${COMPOSER_INSTALLER_VERSION}/web/installer" | php -- --quiet --install-dir=/usr/local/bin --filename=composer --version="${COMPOSER_VERSION}"

RUN --mount=type=bind,source=.,target=/usr/src/app \
    --mount=type=cache,target=/root/.composer \
    set -eux; \
    cd /usr/src/app; \
    docker/copypath /app/ \
        bin/console \
        config \
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

FROM caddy:2.6.4-builder-alpine AS caddy-builder

RUN xcaddy build \
    --with github.com/abiosoft/caddy-yaml

FROM base

ARG ALPINE_VERSION='3.17'
ARG NGINX_VERSION='1.23.4'
ARG NGINX_PKG_RELEASE='-r1'
ARG NGINX_MODULES_TAG='1.23.4'
ARG NGINX_BROTLI_VERSION='1.0.0'
ARG NGINX_HEADERS_MORE_VERSION='0.34'
ARG NGINX_BUILD_TAG='v1.23.4'

RUN set -eux; \
    apk add --no-cache \
        su-exec \
        libc6-compat; \
    curl -fsSL 'https://nginx.org/keys/nginx_signing.rsa.pub' -o /etc/apk/keys/nginx_signing.rsa.pub; \
    apk add -X "https://nginx.org/packages/mainline/alpine/v${ALPINE_VERSION}/main" --no-cache "nginx=${NGINX_VERSION}${NGINX_PKG_RELEASE}"; \
    for apk in \
        "nginx-module-brotli-${NGINX_VERSION}.${NGINX_BROTLI_VERSION}${NGINX_PKG_RELEASE}-x86_64.apk" \
        "nginx-module-headers-more-${NGINX_VERSION}.${NGINX_HEADERS_MORE_VERSION}${NGINX_PKG_RELEASE}-x86_64.apk" \
    ; do \
        curl -fsSL "https://github.com/bohanyang/nginx-modules/releases/download/${NGINX_MODULES_TAG}/${apk}" -o /tmp/nginx-module.apk; \
        apk add --no-cache --allow-untrusted /tmp/nginx-module.apk; \
    done; \
    rm -rfv /tmp/nginx-module.apk; \
    for script in \
        nginx-conf \
        nginx-pull-config \
    ; do \
        curl -fsSL "https://github.com/manyou-io/nginx-build/raw/${NGINX_BUILD_TAG}/root/usr/local/bin/${script}" \
            -o "/usr/local/bin/${script}"; \
        chmod a+rx "/usr/local/bin/${script}"; \
    done

COPY --from=builder \
    /usr/local/bin/composer \
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
