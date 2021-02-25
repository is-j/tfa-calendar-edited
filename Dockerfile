FROM composer:latest as build
WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev

FROM php:8-apache as prod
RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 443
COPY --from=build /app /var/www/html/
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY .env.prod /var/www/html/.env
RUN chmod 777 -R /var/www/html/storage/
RUN echo "Listen 443" >> /etc/apache2/ports.conf
RUN chown -R www-data:www-data /var/www/html/
RUN a2enmod rewrite
CMD sh /var/www/html/docker/startup.sh

