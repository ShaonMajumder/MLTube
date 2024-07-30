Challenge 1: solved
CMD ["sh", "-c", "\
        mkdir -p /var/www/Object-Detection-YoloV4/resources && \
        if [ ! -f /var/www/Object-Detection-YoloV4/resources/coco.names ]; then curl -o /var/www/Object-Detection-YoloV4/resources/coco.names https://raw.githubusercontent.com/pjreddie/darknet/master/data/coco.names; else echo 'coco.names already exists'; fi && \
        if [ ! -f /var/www/Object-Detection-YoloV4/resources/yolov4.cfg ]; then curl -o /var/www/Object-Detection-YoloV4/resources/yolov4.cfg https://raw.githubusercontent.com/AlexeyAB/darknet/master/cfg/yolov4.cfg; else echo 'yolov4.cfg already exists'; fi && \
        if [ ! -f /var/www/Object-Detection-YoloV4/resources/yolov4.weights ]; then wget -O /var/www/Object-Detection-YoloV4/resources/yolov4.weights https://github.com/AlexeyAB/darknet/releases/download/yolov4/yolov4.weights; else echo 'yolov4.weights already exists'; fi && \
        php-fpm"]

is used, because, docker container in dockerFile, can not rewrite mounted volume:
        volumes:
                - ./public_html:/var/www/public_html


CMD is used to run outside of container in host system, so it can rewrite the system.

NB: Mounted voulme  - ./public_html:/var/www/public_html is also necessary, without it, files do not download also externally

Challenge 2: solved
chmod +x entrypoint.sh
in yml:
entrypoint: /usr/local/bin/entrypoint.sh ->
#!/bin/sh

# Start PHP-FPM in the background
php-fpm &

# Start the queue worker
while true; do
    php artisan queue:work --sleep=0 --timeout=60000
    sleep 1
done


is used because, entrypoint is prioritized over CMD, if entrypoint is not mentioned only CMD is run.
this entrypoint file ensures php runs and as well as the queue worker

Challenge 3 : solved
permission related issue in venor file,
in dockerfile:
RUN chown -R www-data:www-data /var/www/public_html
RUN composer install

if mounted volume - ./public_html:/var/www/public_html exists, internaal docer command can not change ownership, host ownership is copied.
        solution: do not mount the volume.
                else run composer as superuser
