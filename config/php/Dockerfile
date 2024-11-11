FROM php:8.2-fpm
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./php.ini /usr/local/etc/php/php.ini

RUN mkdir -p /var/www/html/public/img/profile && \
    chown -R www-data:www-data /var/www/html/public/img/profile && \
    chmod -R 775 /var/www/html/public/img/profile
