FROM php:8.3-cli-alpine AS base

ARG PHP_EXT_INSTALLER_VERSION="2.1.67"
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
        pdo_pgsql \
        gmp

FROM node:18-alpine AS assets-builder

COPY package.json pnpm-lock.yaml /app/

RUN --mount=type=cache,target=/root/.local/share/pnpm/store \
    --mount=type=cache,target=/root/.cache/pnpm \
    set -eux; \
    corepack enable; \
    corepack prepare pnpm@latest --activate; \
    cd /app; \
    pnpm install

RUN --mount=type=bind,source=.,target=/usr/src/app \
    set -eux; \
    cd /usr/src/app; \
    docker/copypath /app/ \
        assets \
        tsconfig.json \
        vite.config.js; \
    cd /app; \
    pnpm build

FROM base AS builder

ARG S6_OVERLAY_VERSION="3.1.6.2"
RUN mkdir /overlay; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-noarch.tar.xz | tar -C /overlay -xJpf -; \
    curl -fsSL https://github.com/just-containers/s6-overlay/releases/download/v${S6_OVERLAY_VERSION}/s6-overlay-x86_64.tar.xz | tar -C /overlay -xJpf -

ARG COMPOSER_VERSION="2.6.5"
ARG COMPOSER_INSTALLER_VERSION="70527179915d55b3811bebaec55926afd331091b"
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

FROM base

ARG ALPINE_VERSION='3.18'
ARG NGINX_VERSION='1.25.3'
ARG NGINX_PKG_RELEASE='-r1'
ARG NGINX_MODULES_TAG='1.23.4'
ARG NGINX_BROTLI_VERSION='1.0.0'
ARG NGINX_HEADERS_MORE_VERSION='0.34'
ARG NGINX_BUILD_TAG='v1.25.2'

RUN set -eux; \
    apk add --no-cache \
        su-exec \
        libc6-compat; \
    curl -fsSL 'https://nginx.org/keys/nginx_signing.rsa.pub' -o /etc/apk/keys/nginx_signing.rsa.pub; \
    apk add -X "https://nginx.org/packages/mainline/alpine/v${ALPINE_VERSION}/main" --no-cache "nginx=${NGINX_VERSION}${NGINX_PKG_RELEASE}"; \
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

COPY --from=builder /overlay /

COPY --from=builder --chown=www-data:www-data /app /app/
COPY --from=assets-builder --chown=www-data:www-data /app/public/build /app/public/build/

COPY docker/root /

ENV S6_CMD_WAIT_FOR_SERVICES_MAXTIME="60000" \
    S6_LOGGING_SCRIPT="n20 s1000000" \
    APP_RUNTIME_WORKERS="1"

ENTRYPOINT ["/init"]
