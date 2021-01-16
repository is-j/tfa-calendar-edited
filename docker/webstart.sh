#!/bin/sh
if ! pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start php-fpm
fi

while ! pgrep -x "php-fpm" > /dev/null
do
    if pgrep -x "php-fpm" > /dev/null && ! pgrep -x "nginx" > /dev/null
    then
        supervisorctl start nginx
    fi
done

if pgrep -x "php-fpm" > /dev/null && ! pgrep -x "nginx" > /dev/null
then
    supervisorctl start nginx
fi

