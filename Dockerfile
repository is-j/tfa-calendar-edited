FROM php:8.0-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php-fpm.conf "/usr/local/etc/php-fpm.conf"

RUN apk add --no-cache nginx supervisor wget
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app
RUN chown -R www-data: /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
  /usr/local/bin/composer install --optimize-autoloader --no-dev && \
  php artisan optimize:clear

RUN chmod +x /app/docker/controller.sh
CMD sh /app/docker/startup.sh