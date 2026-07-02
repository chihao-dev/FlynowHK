FROM php:8.2-cli

WORKDIR /var/www

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Laravel permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php -S 0.0.0.0:10000 -t public
