# Use the official PHP image as a base image
FROM php:7.3-fpm
WORKDIR /var/www/public_html/
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
    wget \
    libonig-dev \
    ffmpeg \
    python3 \
    python3-pip

RUN apt-get update && apt-get install -y netcat

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs
    

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# installing python modules
WORKDIR /var/www/Object-Detection-YoloV4
COPY Object-Detection-YoloV4/requirements.txt /var/www/Object-Detection-YoloV4/requirements.txt
RUN pip3 install -r requirements.txt

WORKDIR /var/www/public_html/
COPY public_html/ /var/www/public_html/
COPY Object-Detection-YoloV4/ /var/www/Object-Detection-YoloV4/

# currently not working because mounted volume get ownership of hosted system.
RUN chown -R www-data:www-data /var/www/public_html
RUN composer install
#---- currently not working because mounted volume get ownership of hosted system.

RUN chown -R www-data:www-data /var/www/public_html/storage/
RUN chmod -R 777 /var/www/public_html/storage/
RUN chown -R www-data:www-data /var/www/Object-Detection-YoloV4/



RUN php artisan storage:link
RUN npm install
RUN npm run dev

USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]