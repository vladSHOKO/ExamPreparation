# ========================
# 1. Сборка фронтенда (Vite)
# ========================
FROM node:20-alpine AS build

WORKDIR /app

# копируем package.json для кеша зависимостей
COPY package*.json vite.config.* ./

RUN npm ci

# копируем исходники
COPY . .

# билд ассетов
RUN npm run build

# ========================
# 2. PHP (основной контейнер)
# ========================
FROM php:8.3-fpm

# системные зависимости для PHP и PostgreSQL
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

# сначала только composer.json и composer.lock для кеша
COPY composer.json composer.lock ./

# устанавливаем зависимости без скриптов
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# копируем весь проект
COPY . .

# копируем собранные ассеты
COPY --from=build /app/public/build ./public/build

# создаем нужные папки и даем права
RUN mkdir -p storage/framework/cache storage/framework/views storage/framework/sessions bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# выполняем artisan после того как все уже на месте
RUN php artisan package:discover --ansi \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache
