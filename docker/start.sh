#!/bin/sh
set -e

PORT="${PORT:-10000}"

# O Render define a porta pela variável PORT
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Certificado da CA do banco passado como texto (ex.: Aiven)
if [ -n "$MYSQL_SSL_CA_PEM" ]; then
    printf '%s\n' "$MYSQL_SSL_CA_PEM" > /tmp/mysql-ca.pem
    export MYSQL_ATTR_SSL_CA=/tmp/mysql-ca.pem
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

exec apache2-foreground
