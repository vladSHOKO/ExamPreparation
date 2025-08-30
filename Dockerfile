# ========================
# 1. Сборка фронтенда (vite)
# ========================
FROM node:20-alpine AS build

WORKDIR /app

# копируем package.json и lock-файл
COPY package*.json vite.config.* ./

# устанавливаем зависимости
RUN npm ci

# копируем исходники
COPY . .

# собираем ассеты
RUN npm run build


# ========================
# 2. PHP (основной контейнер)
# ========================
FROM php:8.3-fpm

# устанавливаем зависимости и расширения
RUN apt-get update && apt-get install -y \
        libpq-dev \
        git \
        unzip \
        zip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean

WORKDIR /var/www

# устанавливаем composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# копируем composer.* и ставим зависимости
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# копируем остальной код приложения
COPY . .

# копируем собранные ассеты из build stage
COPY --from=build /app/public/build ./public/build

# оптимизация Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# выставляем владельца для нужных директорий
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache \
    /var/www/public
