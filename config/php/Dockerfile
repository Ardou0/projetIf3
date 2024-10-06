FROM php:8.2-fpm
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf
