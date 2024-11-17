FROM dunglas/frankenphp:1.3-php8.3 AS php_base

SHELL ["/bin/bash", "-eux", "-o", "pipefail", "-c"]

WORKDIR /app

RUN apt-get update; \
	apt-get install -y --no-install-recommends \
		acl \
		file \
		gettext \
		git \
		jq \
	; \
	rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
		@composer \
		apcu \
		bcmath \
		event \
		gmp \
		intl \
		opcache \
		openswoole \
		pcntl \
		pdo_mysql \
		pdo_pgsql \
		sockets \
		zip \
	;

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link --chmod=755 docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link docker/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["docker-entrypoint"]

FROM node:22 AS assets_builder

SHELL ["/bin/bash", "-eux", "-o", "pipefail", "-c"]

WORKDIR /app

COPY --link package.json pnpm-lock.yaml ./

RUN --mount=type=cache,target=/root/.local/share/pnpm/store \
	--mount=type=cache,target=/root/.cache/pnpm \
	corepack enable; \
	corepack prepare pnpm@latest --activate; \
	pnpm install

RUN --mount=type=bind,source=.,target=/usr/src/app \
	cp -R /usr/src/app/{assets,tsconfig.json,vite.config.mjs} ./; \
	pnpm build

FROM php_base

ENV APP_ENV prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

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
