FROM ubuntu:18.04

MAINTAINER kurenaif

ENV DEBIAN_FRONTEND noninteractive
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2

RUN sed -i 's@archive.ubuntu.com@ftp.jaist.ac.jp/pub/Linux@g' /etc/apt/sources.list

COPY ./ubuntu/sources.list /etc/apt/sources.list

RUN apt-get update --fix-missing && apt-get -y install \
	php7.2 \
	php7.2-fpm php7.2-mysql php7.2-zip php7.2-gd \
	git \
	php7.2-mbstring php7.2-xml \
	php7.2-curl php7.2-json \
	apache2 \
	mysql-server \
	libapache2-mod-php \
	redis-server \
	wget \
	vsftpd \
	tzdata

# Shift timezone to Asia/Tokyo.
ENV TZ Asia/Tokyo

RUN wget -P /usr/local/bin https://github.com/mailhog/MailHog/releases/download/v1.0.0/MailHog_linux_amd64
RUN chmod +x /usr/local/bin/MailHog_linux_amd64

WORKDIR /tmp

RUN apt build-dep -y imagemagick
RUN wget http://www.imagemagick.org/download/releases/ImageMagick-6.9.2-0.tar.xz
RUN tar Jxfv ImageMagick-6.9.2-0.tar.xz
WORKDIR /tmp/ImageMagick-6.9.2-0
RUN ./configure
RUN make install
RUN ldconfig /usr/local/lib

RUN a2enmod rewrite
RUN a2enmod proxy_http

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

COPY ./apache2/apache2.conf /etc/apache2/apache2.conf
COPY ./apache2/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY ./ubuntu/vsftpd.conf /etc/vsftpd.conf

COPY ./docker-entrypoint.sh /

RUN chmod a-w /var/www

COPY src /var/www/laravel

RUN chown -R www-data /var/www/laravel/storage/logs/
RUN chown -R www-data /var/www/laravel/storage/framework/
RUN chown -R www-data /var/www/laravel/storage/app/public/
RUN chown -R www-data /var/www/laravel/bootstrap/cache/

EXPOSE 80

WORKDIR /var/www/laravel

RUN composer install

ENTRYPOINT ["/docker-entrypoint.sh"]

