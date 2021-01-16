#!/bin/sh
if ! pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start php-fpm
fi
if pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start nginx
fi
