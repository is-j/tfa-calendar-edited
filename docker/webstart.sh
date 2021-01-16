#!/bin/sh
if ! pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start php-fpm
fi

while ! pgrep -x "php-fpm" > /dev/null
do
    sleep .01
done

if ! pgrep -x "nginx" > /dev/null
then
    supervisorctl start nginx
fi

