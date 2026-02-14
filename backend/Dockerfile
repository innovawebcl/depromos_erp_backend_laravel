FROM php:8.3-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Create fresh Laravel 12 skeleton (requires internet when building)
RUN composer create-project laravel/laravel:^12.0 /app --no-interaction

# Copy our patch on top
COPY patch/ /patch/
RUN cp -R /patch/. /app/

# JWT lib
RUN composer require firebase/php-jwt:^6.10 --no-interaction

# Env defaults
RUN cp .env.example .env

# App key
RUN php artisan key:generate

# Expose and run
EXPOSE 8000

CMD php artisan migrate --seed && php artisan serve --host=0.0.0.0 --port=8000
