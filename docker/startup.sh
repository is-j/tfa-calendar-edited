#!/bin/sh
sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf
/usr/bin/supervisord -c /app/docker/supervisord.conf
while :
do
    if ["$(ps aux | grep php-fpm: master process)" -ne ""]
    then
        supervisorctl start nginx
        exit
    fi
done