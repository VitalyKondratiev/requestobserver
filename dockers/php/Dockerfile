FROM php:7.3-fpm

RUN apt-get update
RUN apt-get install wget git -y

RUN pecl install xdebug-2.9.6 && docker-php-ext-enable xdebug
ADD ./php.ini /usr/local/etc/php/conf.d/custom-php.ini

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

RUN usermod -u 1000 www-data

CMD ["sh", "-c", "composer install; php-fpm -F -R"]