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

# Timezone
ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Env defaults
RUN cp .env.example .env

# App key
RUN php artisan key:generate

# Expose and run
EXPOSE 8000

# Wait for DB to be ready, then migrate and serve
CMD bash -c 'echo "Waiting for database..."; while ! php artisan migrate:status > /dev/null 2>&1; do sleep 2; done; php artisan migrate --force --seed && php artisan serve --host=0.0.0.0 --port=8000'
