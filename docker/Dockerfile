FROM php:8.3-cli-alpine AS php-base

ARG PHP_EXT_INSTALLER_VERSION="2.2.5"
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
        pdo_mysql \
        pdo_pgsql \
        gmp

FROM node:20-alpine AS assets-builder

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

FROM php-base AS builder

ARG COMPOSER_VERSION="2.7.2"
ARG COMPOSER_INSTALLER_VERSION="f3108f64b4e1c1ce6eb462b159956461592b3e3e"
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

FROM php-base

RUN set -eux; \
    apk add --no-cache \
        su-exec \
        libc6-compat

COPY --from=builder /usr/local/bin/composer /usr/local/bin/
COPY --from=builder --chown=www-data:www-data /app /app/
COPY --from=assets-builder --chown=www-data:www-data /app/public/build /app/public/build/

COPY docker/root /

ENTRYPOINT ["/entrypoint"]
