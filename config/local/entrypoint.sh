#!/bin/sh

cd /var/www/html

composer install --no-interaction --no-progress --prefer-dist

#rm -rf ./storage/app/inventory/*

php artisan migrate:fresh --seed

