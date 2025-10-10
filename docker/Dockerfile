# Use the base PHP 8.2 image with Apache
FROM php:8.2-apache

# Install system dependencies required for PHP and other tools
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    zip \
    unzip

# Install Node.js and npm to run frontend commands like Vite
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install and enable Xdebug for PHP debugging
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy custom Xdebug configuration
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Clean apt cache to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required for the Laravel application
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite module for friendly URLs
RUN a2enmod rewrite

# Install Composer for PHP dependency management
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy application contents to the container (this is overridden by volumes in docker-compose)
COPY . /var/www/html

# Copy appropriate permissions for the www-data user
COPY --chown=www-data:www-data . /var/www/html

# Configure Apache document root to point to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Create necessary directories and assign permissions for Laravel
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache
