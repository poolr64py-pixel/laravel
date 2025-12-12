#!/bin/bash
cd /home/terrasnoparaguay/htdocs/www.terrasnoparaguay.com

PHP_USER=$(ps aux | grep "php-fpm: pool" | grep -v grep | head -1 | awk '{print $1}')
echo "Usuário PHP: $PHP_USER"

chown -R $PHP_USER:$PHP_USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan cache:clear
php artisan config:clear
php artisan view:clear

systemctl restart php8.3-fpm nginx

echo "✓ Concluído!"
