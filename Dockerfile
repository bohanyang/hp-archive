FROM dunglas/frankenphp:1-php8.3 AS frankenphp_base

SHELL ["/bin/bash", "-eux", "-o", "pipefail", "-c"]

WORKDIR /app

RUN apt-get update; \
	apt-get install -y --no-install-recommends \
		acl \
		file \
		gettext \
		git \
	; \
	rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
		@composer \
		apcu \
		bcmath \
        gmp \
		intl \
		opcache \
		pdo_mysql \
        pdo_pgsql \
		zip \
	;

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --link frankenphp/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link frankenphp/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

FROM node:20 AS assets_builder

SHELL ["/bin/bash", "-eux", "-o", "pipefail", "-c"]

WORKDIR /app

COPY --link package.json pnpm-lock.yaml ./

RUN --mount=type=cache,target=/root/.local/share/pnpm/store \
    --mount=type=cache,target=/root/.cache/pnpm \
    corepack enable; \
    corepack prepare pnpm@latest --activate; \
    pnpm install

RUN --mount=type=bind,source=.,target=/usr/src/app \
    cp -R /usr/src/app/{assets,tsconfig.json,vite.config.js} ./; \
    pnpm build

FROM frankenphp_base

ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="import worker.Caddyfile"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link frankenphp/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/
COPY --link frankenphp/worker.Caddyfile /etc/caddy/worker.Caddyfile

COPY --link composer.* symfony.* ./
RUN --mount=type=cache,target=/root/.composer \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

RUN --mount=type=bind,source=.,target=/usr/src/app \
	cp -R /usr/src/app/{bin,config,migrations,public,src,templates,.env} ./; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod a+rx bin/console; sync;

COPY --from=assets_builder /app/public/build/ /app/public/build/
