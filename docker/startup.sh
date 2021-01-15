#!/bin/sh

sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

php-fpm -D && nginx -g "daemon off;"

/usr/bin/supervisord -c /app/docker/supervisord.conf