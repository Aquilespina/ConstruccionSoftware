# Multi-stage build for Laravel on Render
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY ConstruccionSoftware/Veterinaria/package*.json ./
RUN npm ci
COPY ConstruccionSoftware/Veterinaria .
RUN npm run build

FROM php:8.2-apache
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libpng-dev libzip-dev libonig-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel application
COPY ConstruccionSoftware/Veterinaria .

# Copy built assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Apache
RUN a2enmod rewrite
RUN echo '<VirtualHost *:$PORT>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create start script
RUN echo '#!/bin/bash\n\
# Replace PORT placeholder in Apache config\n\
sed -i "s/\$PORT/$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/start-server && chmod +x /usr/local/bin/start-server

EXPOSE $PORT

CMD ["/usr/local/bin/start-server"]