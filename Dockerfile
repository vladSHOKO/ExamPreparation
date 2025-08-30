# ========================
# 1. Сборка фронтенда (Vite)
# ========================
FROM node:20-alpine AS build

WORKDIR /app

COPY package*.json vite.config.* ./
RUN npm ci
COPY . .
RUN npm run build

# ========================
# 2. PHP (основной контейнер)
# ========================
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        libpq-dev \
        git \
        unzip \
        zip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# копируем весь проект
COPY . .

# копируем собранные ассеты
COPY --from=build /app/public/build ./public/build

# создаем нужные папки и даем права
RUN mkdir -p storage/framework/cache storage/framework/views storage/framework/sessions bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ставим зависимости composer без оптимизаций и без скриптов
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts

# создаём нужные папки
RUN mkdir -p storage/framework/{cache,views,sessions} bootstrap/cache public/tasks \
    && chown -R www-data:www-data storage bootstrap/cache public/tasks \
    && chmod -R 775 storage bootstrap/cache public/tasks

RUN echo "upload_max_filesize = 50M\npost_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini
