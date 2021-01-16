#!/bin/sh
if ["$(ps aux | grep php-fpm: master process)" -eq ""]
then
    supervisorctl start php-fpm
fi

while ["$(ps aux | grep php-fpm: master process)" -eq ""]
do
    if ["$(ps aux | grep php-fpm: master process)" -ne ""] && ["$(ps aux | grep nginx)" -eq ""]
    then
        supervisorctl start nginx
    fi
done

if ["$(ps aux | grep php-fpm: master process)" -ne ""] && ["$(ps aux | grep nginx)" -eq ""]
then
    supervisorctl start nginx
fi

