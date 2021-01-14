FROM php:7.4

ENV PORT=8080
ENV HOST=0.0.0.0

RUN apt-get update -y \
  && apt-get install --no-install-recommends -y openssl zip unzip git libonig-dev \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

RUN ["/bin/bash", "-c", "set -o pipefail && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer"]
RUN docker-php-ext-install pdo pdo_mysql mbstring
WORKDIR /app
COPY . /app
RUN composer validate && composer install

EXPOSE 8080
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]