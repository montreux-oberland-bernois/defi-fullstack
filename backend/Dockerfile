FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Xdebug for code coverage
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Configure Xdebug
RUN echo "xdebug.mode=coverage" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"

# Configure OPcache
RUN echo "opcache.memory_consumption=128" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.interned_strings_buffer=8" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.max_accelerated_files=4000" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.revalidate_freq=2" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.fast_shutdown=1" >> "$PHP_INI_DIR/conf.d/opcache.ini"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Create required directories
RUN mkdir -p bootstrap/cache storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 775 bootstrap/cache storage

# Generate autoloader and run scripts
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage 2>/dev/null || true \
    && chmod -R 755 /var/www/html/bootstrap/cache 2>/dev/null || true

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
