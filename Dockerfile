FROM php:8.1.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    vim \
    ca-certificates

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

RUN usermod -u 1000 www-data

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY certs/cacert.pem /usr/lib/ssl/certs/cacert.pem

COPY certs/ca-certificates.crt /usr/local/share/ca-certificates/ca-certificates.crt

# RUN apt-get upgrade ca-certificates

# RUN pecl install -o -f redis-6.0.0 \
#     &&  rm -rf /tmp/pear \
#     &&  docker-php-ext-enable redis

USER www-data

EXPOSE 9000