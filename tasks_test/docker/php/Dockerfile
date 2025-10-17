FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt update && apt install -y \
    git unzip libicu-dev libzip-dev zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
