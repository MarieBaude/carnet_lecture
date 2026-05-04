FROM php:8.3-fpm-alpine

LABEL maintainer="votre-nom@email.com"
LABEL description="PHP 8.3 avec extensions PostgreSQL pour Laravel"

# Installation des dépendances système
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    icu-dev \
    oniguruma-dev \
    libpng-dev \
    libxml2-dev \
    bash

# Configuration et installation des extensions PHP
RUN docker-php-ext-configure zip && \
    docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    intl \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration PHP pour le développement
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    echo "memory_limit = 256M" >> "$PHP_INI_DIR/php.ini" && \
    echo "max_execution_time = 300" >> "$PHP_INI_DIR/php.ini"

# Création de l'utilisateur non-root
RUN addgroup -g 1000 laravel && \
    adduser -G laravel -g laravel -s /bin/sh -D laravel

WORKDIR /var/www

# Configuration des permissions
RUN chown -R laravel:laravel /var/www
USER laravel

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]