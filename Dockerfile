# Gunakan PHP 8.2 dengan ekstensi yang dibutuhkan Laravel
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copy semua file project
COPY . .

# Install dependencies Laravel
RUN composer install --no-interaction --optimize-autoloader --prefer-dist

# Expose port untuk artisan serve
EXPOSE 8000

# Command default untuk menjalankan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
