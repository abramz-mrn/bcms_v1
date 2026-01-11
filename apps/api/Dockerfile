# Base image
FROM php:8.3-cli-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql pcntl bcmath intl opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Development stage
FROM base AS development

# Install development dependencies
RUN apk add --no-cache nodejs npm

# Copy application
COPY .  .

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Expose port
EXPOSE 8000

# Start development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# Production stage
FROM base AS production

# Install RoadRunner
RUN curl -sSL https://github.com/roadrunner-server/roadrunner/releases/download/v2024.1.1/roadrunner-2024.1.1-linux-amd64.tar.gz | tar -xz \
    && mv roadrunner-2024.1.1-linux-amd64/rr /usr/local/bin/rr \
    && chmod +x /usr/local/bin/rr \
    && rm -rf roadrunner-2024.1.1-linux-amd64

# Copy application
COPY .  .

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Octane with RoadRunner
CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000"]