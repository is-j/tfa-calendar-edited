#!/bin/sh
supervisorctl start php-fpm

while [[ $(ps -aux | grep "[p]hp-fpm: master process") == "" ]];
do
    if [[ $(ps -aux | grep "[p]hp-fpm: master process") != "" ]]; then
        supervisorctl start nginx
    fi
done