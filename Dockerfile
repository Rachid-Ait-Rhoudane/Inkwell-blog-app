# Composer stage
FROM composer:2.7.1 AS composer

WORKDIR /app

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Node stage
FROM node:24-alpine AS node

ARG APP_NAME

ENV VITE_APP_NAME=${APP_NAME}

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

# Final runtime
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    libzip-dev \
    linux-headers \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    sockets

COPY --from=composer /app /var/www/html
COPY --from=node /app/public/build /var/www/html/public/build

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
