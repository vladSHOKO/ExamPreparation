# ========================
# 1. Сборка фронтенда (vite)
# ========================
FROM node:20-alpine AS build

WORKDIR /app

# копируем только package.json для кеша зависимостей
COPY package*.json vite.config.* ./

RUN npm ci

# копируем исходники (чтобы vite видел ресурсы)
COPY . .

# билд ассетов
RUN npm run build


# ========================
# 2. PHP (основной контейнер)
# ========================
FROM php:8.3-fpm

# зависимости PHP
RUN apt-get update && apt-get install -y \
        libpq-dev \
        git \
        unzip \
        zip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean

WORKDIR /var/www

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# сначала только composer.* (для кеша)
COPY composer.json composer.lock ./

# ставим зависимости без скриптов (artisan ещё нет)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# копируем весь проект
COPY . .

# копируем собранные ассеты
COPY --from=build /app/public/build ./public/build

# выполняем artisan только теперь
RUN php artisan package:discover --ansi \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# права для нужных папок
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache \
    /var/www/public
