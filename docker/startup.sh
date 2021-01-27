#!/bin/sh
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