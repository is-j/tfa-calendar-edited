#!/bin/sh
if ! pgrep -x "php-fpm" > /dev/null
then
    supervisorctl start php-fpm
fi
echo "hello1"
while ! pgrep -x "php-fpm" > /dev/null
do
    sleep .01
done

echo "hello2"
if ! pgrep -x "nginx" > /dev/null
then
    supervisorctl start nginx
fi

echo "hello3"
