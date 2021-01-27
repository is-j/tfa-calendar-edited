FROM php:7.4-fpm-alpine
RUN docker-php-ext-install -j "$(nproc)" opcache
RUN set -ex; \
  { \
  echo "; Cloud Run enforces memory & timeouts"; \
  echo "memory_limit = -1"; \
  echo "max_execution_time = 0"; \
  echo "; File upload at Cloud Run network limit"; \
  echo "upload_max_filesize = 32M"; \
  echo "post_max_size = 32M"; \
  echo "; Configure Opcache for Containers"; \
  echo "opcache.enable = On"; \
  echo "opcache.validate_timestamps = Off"; \
  echo "; Configure Opcache Memory (Application-specific)"; \
  echo "opcache.memory_consumption = 32"; \
  } > "$PHP_INI_DIR/conf.d/cloud-run.ini"
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apk add --no-cache nginx supervisor wget
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
  /usr/local/bin/composer install --optimize-autoloader --no-dev
RUN chown -R www-data: /app
RUN chmod +x /app/docker/controller.sh
RUN cd /app && \
  php artisan route:cache && \
  php artisan view:cache

CMD sh /app/docker/startup.sh