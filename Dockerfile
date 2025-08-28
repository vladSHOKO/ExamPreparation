FROM php:8.3-fpm

# Устанавливаем зависимости и расширения
RUN apt-get update && apt-get install -y \
        libpq-dev \
        git \
        unzip \
        zip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean

WORKDIR /var/www
