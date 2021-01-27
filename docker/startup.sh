#!/bin/sh
RUN sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf
RUN /usr/bin/supervisord -c /app/docker/supervisord.conf
if ["$(ps aux | grep php-fpm: master process)" -eq ""]
then
    supervisorctl start php-fpm
fi
while :
do
    if ["$(ps aux | grep php-fpm: master process)" -ne ""]
    then
        supervisorctl start nginx
        exit
    fi
done