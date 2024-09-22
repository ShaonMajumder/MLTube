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
    python3-pip \
    supervisor \
    cron \
    netcat

# netcat is necessary to test mysql in entrypoint.sh 
    
# Install Redis PHP extension
RUN pecl install redis \
    && docker-php-ext-enable redis
    # && docker-php-ext-install -j$(nproc) bcmath

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs
    
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/public_html/
# COPY public_html/ /var/www/public_html/ -- commented 20-0924

# currently not working because mounted volume get ownership of hosted system.
# these lines need to run in entrypoint.sh again
# RUN chown -R www-data:www-data /var/www/public_html

RUN mkdir -p storage/framework/sessions/
RUN mkdir -p storage/framework/views/
RUN mkdir -p storage/framework/cache/data/
RUN mkdir -p vendor/
RUN mkdir -p var/www/.composer
RUN chown -R www-data:www-data vendor/
# USER www-data  -- commented 20-0924
# RUN composer install  -- commented 20-0924
# USER root  -- commented 20-0924

RUN chown -R www-data:www-data /var/www/public_html/storage/
RUN chmod -R 755 /var/www/public_html/storage/

# RUN php artisan storage:link  -- commented 20-0924
#---- currently not working because mounted volume get ownership of hosted system.
# RUN npm install  -- commented 20-0924
# RUN npm run dev  -- commented 20-0924

# Install Python modules
WORKDIR /var/www/Object-Detection-YoloV4
COPY Object-Detection-YoloV4/requirements.txt /var/www/Object-Detection-YoloV4/requirements.txt
RUN pip3 install -r requirements.txt

# Copy the rest of your application files
COPY Object-Detection-YoloV4/ /var/www/Object-Detection-YoloV4/
RUN mkdir -p /var/www/Object-Detection-YoloV4/io/ \
    && chown -R www-data:www-data /var/www/Object-Detection-YoloV4/io/




    
# Create supervisor log directory with proper permissions
RUN mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/log/supervisor \
    && chmod -R 755 /var/log/supervisor

# Supervisor configuration files
COPY docker/app/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create and ensure permissions for run directories
RUN mkdir -p /var/run/supervisor \
    && chown -R www-data:www-data /var/run/supervisor \
    && chmod -R 755 /var/run/supervisor

# giving permission to cron
RUN mkdir -p /var/run/ \
    && chown -R www-data:www-data /var/run/ \
    && chmod gu+rw /var/run \
    && chmod 755 /var/run/ \
    && chmod gu+s /usr/sbin/cron

# Cron job for Laravel scheduler
COPY docker/app/php/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron

# Log File For Cron
RUN mkdir -p public_html/storage/logs/cron \
    && chown -R www-data:www-data public_html/storage/logs/cron \
    && chmod -R 755 public_html/storage/logs/cron

USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
# CMD ["php-fpm"]
# Run supervisord to manage php-fpm, queue workers, and cron
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]