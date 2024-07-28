#!/bin/sh

# Start PHP-FPM in the background
php-fpm &

# Start the queue worker
while true; do
    php artisan queue:work --sleep=0 --timeout=60000
    sleep 1
done
