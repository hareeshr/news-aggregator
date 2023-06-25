#!/bin/bash

# set env variables
# echo "NEWSAPI_API_KEY=${NEWSAPI_API_KEY}" >> /app/backend/.env
# echo "NYTIMES_API_KEY=${NYTIMES_API_KEY}" >> /app/backend/.env
# echo "GUARDIAN_API_KEY=${GUARDIAN_API_KEY}" >> /app/backend/.env

# php artisan migrate
php artisan key:generate

# Generate application key
php artisan key:generate

# Update the .env file with MySQL environment variables
# php -r "file_put_contents('.env', str_replace(['DB_DATABASE=laravel', 'DB_USERNAME=root', 'DB_PASSWORD='], ['DB_DATABASE=${MYSQL_DATABASE}', 'DB_USERNAME=${MYSQL_DATABASE}', 'DB_PASSWORD=${MYSQL_DATABASE}'], file_get_contents('.env')));"

php artisan cache:clear
php artisan config:clear
php artisan route:clear

php artisan serve --host=0.0.0.0
exec docker-php-entrypoint "$@"