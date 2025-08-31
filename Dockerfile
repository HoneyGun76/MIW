FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy apache configuration
COPY apache2.conf /etc/apache2/apache2.conf

# Enable Apache modules
RUN a2enmod rewrite headers

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Create PDF storage directory and set permissions
RUN mkdir -p /tmp/pdfs && chmod -R 777 /tmp/pdfs

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /tmp/pdfs

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
