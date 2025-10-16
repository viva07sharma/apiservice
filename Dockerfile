# Use official PHP FPM image
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    ca-certificates \
    libcurl4-openssl-dev \
    libxml2-dev \
 && docker-php-ext-install intl pdo_mysql mbstring xml zip curl \
 && pecl install xdebug redis \
 && docker-php-ext-enable xdebug redis \
 && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set Symfony environment variables
ENV APP_ENV=dev
ENV APP_DEBUG=1

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for caching
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --no-scripts --no-progress --prefer-dist --optimize-autoloader

# Copy rest of application code with correct ownership
COPY --chown=www-data:www-data . .

# Expose PHP-FPM port
EXPOSE 9000

# Run PHP-FPM as default
CMD ["php-fpm"]
