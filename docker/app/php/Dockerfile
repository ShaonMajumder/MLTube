FROM php:7.4-fpm
WORKDIR /var/www/public_html/

# Install Redis PHP extension, Node.js, and other PHP extensions
RUN --mount=type=cache,target=/var/cache/apt \
    --mount=type=cache,target=/var/lib/apt \
    apt-get update && apt-get install -y \
    build-essential \
    cron \
    curl \
    ffmpeg \
    gifsicle \
    git \
    jpegoptim \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libpng-dev \
    locales \
    ## NB: netcat is not used by internaal package only for mysql to test mysql in entrypoint.sh 
    netcat \
    optipng \
    python3 \
    python3-pip \
    speedtest-cli \
    supervisor \
    unzip \
    vim \
    zip \
    && apt-get update \
    && pecl install redis  \
    && docker-php-ext-enable redis \
    && curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/public_html/
# for laravel cacheed view, sessions, data
RUN mkdir -p storage/framework/sessions/ \
    storage/framework/views/ \
    storage/framework/cache/data/ \
    # for composer
    vendor/ \
    var/www/.composer \
    && chown -R www-data:www-data vendor/ \
    # for laravel storages directory permissions
    && chown -R www-data:www-data /var/www/public_html/storage/ \
    && chmod -R 755 /var/www/public_html/storage/ \
    # Create supervisor log directory with proper permissions
    && mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/log/supervisor \
    && chmod -R 755 /var/log/supervisor

# Supervisor configuration files
COPY docker/app/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create and ensure permissions for run directories
RUN mkdir -p /var/run/supervisor \
    && chown -R www-data:www-data /var/run/supervisor \
    && chmod -R 755 /var/run/supervisor \
    # giving permission to cron
    && mkdir -p /var/run/ \
    && chown -R www-data:www-data /var/run/ \
    && chmod gu+rw /var/run \
    && chmod 755 /var/run/ \
    && chmod gu+s /usr/sbin/cron

# Cron job for Laravel scheduler
COPY docker/app/php/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron \
    # Log File For Cron
    && mkdir -p public_html/storage/logs/cron \
    && chown -R www-data:www-data public_html/storage/logs/cron \
    && chmod -R 755 public_html/storage/logs/cron

# Install Machine Learning Python modules
WORKDIR /var/www/Object-Detection-YoloV4
COPY Object-Detection-YoloV4/requirements.txt /var/www/Object-Detection-YoloV4/requirements.txt
RUN mkdir -p /var/www/.pip_cache \
    && PIP_CACHE_DIR=/var/www/.pip_cache pip3 install -r requirements.txt
COPY Object-Detection-YoloV4/ /var/www/Object-Detection-YoloV4/
RUN mkdir -p /var/www/Object-Detection-YoloV4/io/ \
    && chown -R www-data:www-data /var/www/Object-Detection-YoloV4/io/

## COPY public_html/ /var/www/public_html/ -- commented 20-0924
##
## --- currently not working because mounted volume get ownership of hosted system.
## these lines need to run in entrypoint.sh again
## RUN chown -R www-data:www-data /var/www/public_html
##
## --- USER www-data  -- commented 20-0924
## RUN composer install  -- commented 20-0924
##  USER root  -- commented 20-0924
##
## --- RUN php artisan storage:link  -- commented 20-0924
## 
## ---- currently not working because mounted volume get ownership of hosted system.
## RUN npm install  -- commented 20-0924
## RUN npm run dev  -- commented 20-0924

USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000

# Run supervisord to manage php-fpm, queue workers, and cron
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]