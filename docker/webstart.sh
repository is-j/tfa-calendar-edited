#!/bin/sh
if [$(ps aux | grep php-fpm) -eq ""]
then
    supervisorctl start php-fpm
fi

while [$(ps aux | grep php-fpm) -eq ""]
do
    if [$(ps aux | grep php-fpm) -ne ""] && [$(ps aux | grep php-fpm) -eq ""]
    then
        supervisorctl start nginx
    fi
done

if [$(ps aux | grep php-fpm) -ne ""] && [$(ps aux | grep php-fpm) -eq ""]
then
    supervisorctl start nginx
fi

