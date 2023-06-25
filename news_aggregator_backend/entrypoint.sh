#!/bin/bash

# set env variables
echo "NEWSAPI_API_KEY=${NEWSAPI_API_KEY}" >> /app/backend/.env
echo "NYTIMES_API_KEY=${NYTIMES_API_KEY}" >> /app/backend/.env
echo "GUARDIAN_API_KEY=${GUARDIAN_API_KEY}" >> /app/backend/.env


# Generate application key
php artisan key:generate

# Update the .env file with MySQL environment variables
php -r "file_put_contents('.env', str_replace(['DB_DATABASE=', 'DB_USERNAME=', 'DB_PASSWORD='], ['DB_DATABASE=${MYSQL_DATABASE}', 'DB_USERNAME=${MYSQL_USER}', 'DB_PASSWORD=${MYSQL_PASSWORD}'], file_get_contents('.env')));"

php artisan route:cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize


php artisan migrate --force

php artisan serve --host=0.0.0.0
exec docker-php-entrypoint "$@"