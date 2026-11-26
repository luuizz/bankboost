#!/bin/sh
set -e

PORT="${PORT:-8080}"

# Porta interna do Apache (o Caddy faz o proxy para ela)
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Uploads ficam num volume; garante a estrutura e o link público
mkdir -p storage/app/public/posts storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
php artisan storage:link --force >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Os caches acima foram gerados como root; o Apache roda como www-data
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force
php artisan db:seed --class=SiteContentSeeder --force

exec apache2-foreground
