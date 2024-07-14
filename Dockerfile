# Use the official PHP image as a base image
FROM php:7.3-fpm

# Set working directory
WORKDIR /var/www_temp

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application files to the temporary directory
COPY public_html/ /var/www_temp/

# Install PHP dependencies
RUN composer install

COPY .env /var/www_temp/.env

# Generate application key
RUN php artisan key:generate

# Copy the built application to the final directory, including hidden files
WORKDIR /var/www/public_html
RUN cp -a /var/www_temp/. /var/www/public_html/
RUN rm -r /var/www_temp/

# Ensure .env file is present in the final directory
COPY .env /var/www/public_html/.env

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www/public_html
RUN chmod -R 777 /var/www/public_html/storage/

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
