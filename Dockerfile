FROM php:8.2-fpm

# system deps
RUN apt-get update && apt-get install -y \
  git curl unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
  && rm -rf /var/lib/apt/lists/*

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
