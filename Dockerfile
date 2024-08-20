# Use the official PHP image as a base image
FROM php:7.3-fpm
WORKDIR /var/www/public_html/
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
    wget \
    libonig-dev \
    ffmpeg \
    python3 \
    python3-pip
    
# Install Redis PHP extension
# RUN pecl install redis \
#     && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y netcat

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs
    
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/public_html/
COPY public_html/ /var/www/public_html/
# currently not working because mounted volume get ownership of hosted system.
# these lines need to run in entrypoint.sh again
# RUN chown -R www-data:www-data /var/www/public_html

RUN mkdir -p storage/framework/sessions/
RUN mkdir -p storage/framework/views/
RUN mkdir -p storage/framework/cache/data/
RUN mkdir -p vendor/
RUN mkdir -p var/www/.composer
RUN chown -R www-data:www-data vendor/
USER www-data
RUN composer install
USER root

RUN chown -R www-data:www-data /var/www/public_html/storage/
RUN chmod -R 755 /var/www/public_html/storage/

RUN php artisan storage:link
#---- currently not working because mounted volume get ownership of hosted system.
RUN npm install
RUN npm run dev


# installing python modules
WORKDIR /var/www/Object-Detection-YoloV4
COPY Object-Detection-YoloV4/ /var/www/Object-Detection-YoloV4/
COPY Object-Detection-YoloV4/requirements.txt /var/www/Object-Detection-YoloV4/requirements.txt
RUN mkdir -p /var/www/Object-Detection-YoloV4/io/
RUN chown -R www-data:www-data /var/www/Object-Detection-YoloV4/io/
RUN pip3 install -r requirements.txt


USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]