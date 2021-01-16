#!/bin/sh
if [$(ps aux | grep php-fpm) -eq ""]
then
    supervisorctl start php-fpm
fi

while [$(ps aux | grep php-fpm) -eq ""];
do
    sleep .05
done

supervisorctl start nginx
