#!/bin/sh
if ! pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start php-fpm
fi

while :
do
    if pgrep -x "php-fpm" > /dev/null
    then
        supervisorctl start nginx
        exit
    fi
done
