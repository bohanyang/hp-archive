FROM mcr.microsoft.com/devcontainers/base:bookworm

ARG USERNAME=vscode
ARG LOGIN_SHELL=fish

SHELL ["/bin/bash", "-eux", "-c"]

COPY ./apt-packages.sh /usr/bin/

RUN apt-packages.sh sury php; \
	apt-packages.sh fish; \
	apt-packages.sh postgresql; \
	apt-get install -y --no-install-recommends \
	dnsutils \
	fish \
	htop \
	iputils-ping \
	jq \
	lsof \
	mtr-tiny \
	net-tools \
	nload \
	patch \
	php8.3-{amqp,apcu,bcmath,cli,curl,dev,fpm,gmp,igbinary,intl,mbstring,mysql,opcache,pgsql,readline,redis,sqlite3,xdebug,xml,zip,swoole,inotify} \
	postgresql-client-17 \
	psmisc \
	rsync \
	screen \
	tree \
	unzip \
	xz-utils \
	; \
	rm -rfv /var/lib/apt/lists/*

RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chsh -s "$(grep "/$LOGIN_SHELL\$" /etc/shells | tail -1)" "$USERNAME"

RUN su "$USERNAME" -c 'curl -fsSL https://fnm.vercel.app/install | bash'
