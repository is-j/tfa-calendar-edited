#!/bin/sh
supervisorctl start php-fpm

while [[ $(ps aux | grep php-fpm) == "" ]];
do
    if [[ $(ps aux | grep php-fpm) != "" ]]; then
        supervisorctl start nginx;
        exit
    fi
done
if [[ $(ps aux | grep php-fpm) != "" ]]; then
    supervisorctl start nginx
fi