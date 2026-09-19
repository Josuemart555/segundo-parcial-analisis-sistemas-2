FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm install

COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        bash \
        git \
        curl \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        mysql-client \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --optimize-autoloader --no-interaction --no-progress \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
