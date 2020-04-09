#!/bin/sh

### start process ###

# a2enmod rewrite
service mysql start
service php7.2-fpm start
service apache2 start
service vsftpd start
redis-server &
/usr/local/bin/MailHog_linux_amd64 &

### mysql settings ###

echo "CREATE DATABASE laravel" | mysql
echo "CREATE USER 'laravel'@'%' IDENTIFIED BY 'passwd';" | mysql
echo "GRANT ALL ON laravel.* TO laravel;" | mysql

cd /var/www/laravel
php artisan migrate:refresh --seed
# chown -R www-data storage/logs/
# chown -R www-data storage/framework/
# chown -R www-data storage/app/public/
# chown -R www-data bootstrap/cache/
cd -

### run argv ###
if [ $# -eq 0 ]; then
	tail -f /dev/null
else
	exec "$@"
fi
