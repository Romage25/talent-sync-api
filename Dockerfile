FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    zip

# Install PHP extensions needed by Laravel + Supabase (Postgres)
RUN docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix Laravel permissions
RUN chmod -R 775 storage bootstrap/cache

# Clear caches (safe for production build)
# RUN php artisan config:clear
# RUN php artisan cache:clear
# RUN php artisan route:clear

# Render requires this port
EXPOSE 10000

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=10000
