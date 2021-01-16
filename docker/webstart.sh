#!/bin/sh
supervisorctl start php-fpm

while [$(ps aux | grep php-fpm) -eq ""];
do
done

supervisorctl start nginx