#!/usr/bin/env sh
# shellcheck shell=dash

set -eu

keyrings=/usr/share/keyrings
os_release=

add() {
    local repo
    repo=$1
    shift

    if [ -z "$os_release" ]; then
        os_release=$(
            . /etc/os-release
            echo "$ID $VERSION_CODENAME $VERSION_ID"
        )
    fi

    # shellcheck disable=SC2086
    "_$repo" $os_release "$@"

    apt-get update
}

fetch() {
    wget -O "$keyrings/$1" "$2"
}

save() {
    echo "$2" >"/etc/apt/sources.list.d/$1.list"
}

_docker() {
    fetch docker.asc "https://download.docker.com/linux/$1/gpg"
    save docker "deb [signed-by=$keyrings/docker.asc] https://download.docker.com/linux/$1 $2 stable"
}

_sury_dpa() {
    fetch sury.gpg https://packages.sury.org/php/apt.gpg
    save "sury-$4" "deb [signed-by=$keyrings/sury.gpg] https://packages.sury.org/$4 $2 main"
    #     cat > /etc/apt/preferences.d/50-sury << 'EOF'
    # Package: openssl
    # Pin: origin "packages.sury.org"
    # Pin-Priority: 100

    # Package: libzip4
    # Pin: origin "packages.sury.org"
    # Pin-Priority: 500

    # Package: lib*
    # Pin: origin "packages.sury.org"
    # Pin-Priority: 100
    # EOF
}

_sury_ppa() {
    fetch sury_ppa.asc 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14AA40EC0831756756D7F66C4F4EA0AAE5267A6C'
    save "sury-$4" "deb [signed-by=$keyrings/sury_ppa.asc] https://ppa.launchpadcontent.net/ondrej/$4/ubuntu $2 main"
    #     cat > "/etc/apt/preferences.d/50-sury-$4" << EOF
    # Package: openssl
    # Pin: release o=LP-PPA-ondrej-$4
    # Pin-Priority: 100

    # Package: libzip4
    # Pin: release o=LP-PPA-ondrej-$4
    # Pin-Priority: 500

    # Package: lib*
    # Pin: release o=LP-PPA-ondrej-$4
    # Pin-Priority: 100
    # EOF
}

_sury() {
    if [ "$1" = ubuntu ]; then
        _sury_ppa "$@"
    elif [ "$1" = debian ]; then
        _sury_dpa "$@"
    fi
}

_mysql() {
    fetch mysql.asc https://repo.mysql.com/RPM-GPG-KEY-mysql-2022
    save "mysql-$4" "deb [signed-by=$keyrings/mysql.asc] https://repo.mysql.com/apt/$1 $2 mysql-$4"
}

_mariadb() {
    fetch mariadb.asc https://mariadb.org/mariadb_release_signing_key.asc
    save "mariadb-$4" "deb [signed-by=$keyrings/mariadb.asc] https://ftp.osuosl.org/pub/mariadb/repo/$4/$1 $2 main"
}

_postgresql() {
    fetch postgresql.asc https://www.postgresql.org/media/keys/ACCC4CF8.asc
    save postgresql "deb [signed-by=$keyrings/postgresql.asc] https://apt.postgresql.org/pub/repos/apt $2-pgdg main"
}

_microsoft() {
    fetch microsoft.asc https://packages.microsoft.com/keys/microsoft.asc
    save microsoft "deb [signed-by=$keyrings/microsoft.asc] https://packages.microsoft.com/$1/$3/prod $2 main"
}

_azure_cli() {
    fetch microsoft.asc https://packages.microsoft.com/keys/microsoft.asc
    save azure-cli "deb [signed-by=$keyrings/microsoft.asc] https://packages.microsoft.com/repos/azure-cli $2 main"
}

_mongodb() {
    fetch "mongodb-$4.asc" "https://www.mongodb.org/static/pgp/server-$4.asc"
    save "mongodb-$4" "deb [signed-by=$keyrings/mongodb-$4.asc] https://repo.mongodb.org/apt/$1 $2/mongodb-org/$4 main"
}

_nginx() {
    fetch nginx.asc https://nginx.org/keys/nginx_signing.key
    save nginx "deb [signed-by=$keyrings/nginx.asc] https://nginx.org/packages/mainline/$1 $2 nginx"
}

_sb_nginx() {
    fetch sb-nginx.asc https://mirrors.xtom.com/sb/nginx/public.key
    save sb-nginx "deb [signed-by=$keyrings/sb-nginx.asc] https://mirrors.xtom.com/sb/nginx $2 main"
}

_haproxy() {
    fetch haproxy.asc https://haproxy.debian.net/bernat.debian.org.gpg
    save "haproxy-$4" "deb [signed-by=$keyrings/haproxy.asc] https://haproxy.debian.net $2-backports-$4 main"
    cat >/etc/apt/preferences.d/50-haproxy <<'EOF'
Package: haproxy haproxy-doc vim-haproxy
Pin: release o=PPA-haproxy
Pin-Priority: 500
EOF
}

_caddy() {
    fetch caddy.asc https://dl.cloudsmith.io/public/caddy/stable/gpg.key
    save caddy "deb [signed-by=$keyrings/caddy.asc] https://dl.cloudsmith.io/public/caddy/stable/deb/debian any-version main"
}

_rabbitmq() {
    fetch rabbitmq-erlang.asc https://dl.cloudsmith.io/public/rabbitmq/rabbitmq-erlang/gpg.E495BB49CC4BBE5B.key
    save rabbitmq-erlang "deb [signed-by=$keyrings/rabbitmq-erlang.asc] https://dl.cloudsmith.io/public/rabbitmq/rabbitmq-erlang/deb/$1 $2 main"

    fetch rabbitmq-server.asc https://dl.cloudsmith.io/public/rabbitmq/rabbitmq-server/gpg.9F4587F226208342.key
    save rabbitmq-server "deb [signed-by=$keyrings/rabbitmq-server.asc] https://dl.cloudsmith.io/public/rabbitmq/rabbitmq-server/deb/$1 $2 main"

    cat >"/etc/apt/preferences.d/50-erlang" <<EOF
Package: erlang*
Pin: origin dl.cloudsmith.io
Pin-Priority: 990
EOF
}

_redis() {
    fetch redis.asc https://packages.redis.io/gpg
    save redis "deb [signed-by=$keyrings/redis.asc] https://packages.redis.io/deb $2 main"
}

_knot() {
    fetch knot.gpg https://deb.knot-dns.cz/apt.gpg
    save knot "deb [signed-by=$keyrings/knot.gpg] https://deb.knot-dns.cz/knot-latest/ $2 main"
}

_fish() {
    if [ "$1" = ubuntu ]; then
        _fish_ppa "$@"
    elif [ "$1" = debian ]; then
        _fish_debian "$@"
    fi
}

_fish_ppa() {
    fetch fish_ppa.asc 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x59FDA1CE1B84B3FAD89366C027557F056DC33CA5'
    save fish_ppa "deb [signed-by=$keyrings/fish_ppa.asc] https://ppa.launchpadcontent.net/fish-shell/release-3/ubuntu $2 main"
}

_fish_debian() {
    fetch "fish_debian_$3.asc" "https://download.opensuse.org/repositories/shells:/fish:/release:/3/Debian_$3/Release.key"
    save "fish_debian_$3" "deb [signed-by=$keyrings/fish_debian_$3.asc] https://download.opensuse.org/repositories/shells:/fish:/release:/3/Debian_$3/ /"
}

if [ "$(id -u)" -ne 0 ]; then
    echo 'This script needs to be run as root.' 1>&2
    exit 1
fi

if [ $# -gt 0 ]; then
    add "$@"
fi
