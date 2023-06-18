FROM php:8.1.11-fpm


# Install dependencies
RUN apt-get update -y
RUN apt-get install -y zip unzip libpq-dev libcurl4-gnutls-dev git openssl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install docker extension
RUN docker-php-ext-install pdo
# RUN docker-php-ext-install mbstring
# RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql bcmath

# Set working directory
WORKDIR /app/backend

COPY . .
RUN composer install

# RUN php artisan migrate

EXPOSE 8000

# CMD php artisan serve --host=0.0.0.0
ENTRYPOINT [ "./entrypoint.sh" ]