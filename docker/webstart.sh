#!/bin/sh
if ! pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start php-fpm
fi

while ! pgrep -x "php-fpm" > /dev/null
do
    echo "checking..."
done

if ! pgrep -x "nginx" > /dev/null
then
    supervisorctl start nginx
fi

