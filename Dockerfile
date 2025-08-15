FROM node:22-trixie AS vite

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

FROM ghcr.io/roadrunner-server/roadrunner:2025 AS roadrunner

FROM php:8.4-cli-trixie

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

RUN --mount=type=bind,from=mlocati/php-extension-installer:2,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions install-php-extensions \
	@composer-2 \
	apcu \
	bcmath \
	event \
	gmp \
	intl \
	opcache \
	pcntl \
	protobuf \
	pdo_mysql \
	pdo_pgsql \
	sockets \
	zip \
	igbinary \
	;

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link --chmod=755 docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

COPY --link composer.* symfony.* ./
RUN --mount=type=cache,target=/root/.composer \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

RUN --mount=type=bind,source=.,target=/usr/src/app \
	cp -R /usr/src/app/{bin,config,migrations,public,src,templates,.env,.rr*.yaml} ./; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod a+rx bin/console; sync;

COPY --from=vite /app/public/build/ /app/public/build/

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

EXPOSE 8080/tcp

CMD ["rr", "serve", "-c", ".rr.yaml"]
