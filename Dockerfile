FROM php:8.1-apache
WORKDIR /var/www/html

COPY src/ /var/www/html/
COPY apache_specific.conf /etc/apache2/sites-available/000-default.conf
COPY apache_main.conf /etc/apache2/apache2.conf

COPY php.ini /usr/local/etc/php/

EXPOSE 80

RUN apt-get update \
    && apt-get install mc --assume-yes \
    && a2enmod rewrite \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql\
    && pecl install xdebug \
    && docker-php-ext-enable xdebug\
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer