# =============================================================================
# Depromos ERP Backend - Dockerfile (Laravel 12 + PHP 8.3)
# Consistente con backend v1.2.0
# =============================================================================

# --- Stage 1: Composer dependencies ---
FROM composer:2 AS vendor

WORKDIR /build

# Crear skeleton Laravel para obtener composer.json base
RUN composer create-project laravel/laravel:^12.0 /build --no-interaction --no-dev --prefer-dist

# Instalar dependencias adicionales del proyecto
RUN composer require firebase/php-jwt:^6.10 --no-interaction --update-no-dev

# --- Stage 2: Application ---
FROM php:8.3-cli

# Metadata
LABEL maintainer="InnovaWeb <dev@innovaweb.cl>"
LABEL description="Depromos ERP Backend API - Laravel 12"
LABEL version="1.2.0"

# System deps (mínimos para producción)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql zip bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

# PHP production optimizations
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Timezone Chile
ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# PHP config
RUN { \
    echo 'date.timezone=America/Santiago'; \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'max_execution_time=60'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /app

# Copy vendor from build stage
COPY --from=vendor /build /app

# Copy our patch on top of Laravel skeleton
COPY patch/ /patch/
RUN cp -R /patch/. /app/ && rm -rf /patch

# Set env from example
RUN cp .env.example .env

# Generate app key
RUN php artisan key:generate

# Optimize for production (config, routes, views cache)
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Create storage directories if missing
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Non-root user for security
RUN groupadd -r appuser && useradd -r -g appuser -d /app appuser \
    && chown -R appuser:appuser /app
USER appuser

EXPOSE 8000

# Healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/api/login -X POST -d '{}' -H 'Content-Type: application/json' -o /dev/null -s -w '%{http_code}' | grep -q '4' || exit 1

# Entrypoint: wait for DB, migrate, seed (idempotent), serve
CMD ["bash", "-c", "\
    echo '[Depromos ERP] Waiting for database...' && \
    until php artisan migrate:status > /dev/null 2>&1; do sleep 2; done && \
    echo '[Depromos ERP] Running migrations...' && \
    php artisan migrate --force && \
    echo '[Depromos ERP] Running seeders...' && \
    php artisan db:seed --force 2>/dev/null || true && \
    echo '[Depromos ERP] Starting server on :8000' && \
    php artisan serve --host=0.0.0.0 --port=8000 \
"]
