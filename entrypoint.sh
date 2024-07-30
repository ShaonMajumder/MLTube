#!/bin/sh

#installation post work, executed from outside the directory


# Function to check if MySQL is ready
wait_for_mysql() {
    local host=${DB_HOST:-$1}
    local port=${DB_PORT:-$2}
    echo "Waiting for MySQL at $host:$port to be available..."
    until nc -z -v -w30 $host $port 2>&1 | grep -q "succeeded"; do
        echo "MySQL is not available yet. Waiting..."
        sleep 5
    done
    echo "MySQL is up and running."
}

# Function to check and set ownership of a directory
check_and_set_ownership() {
    local dir=$1
    local current_user=$(whoami)
    local dir_owner=$(stat -c '%U' $dir)

    if [ "$current_user" != "$dir_owner" ]; then
        echo "Current user $(whoami) does not own $dir. Changing ownership..."
        chown -R $current_user:$current_user $dir
    else
        echo "Current user $(whoami) already owns $dir."
    fi
}

echo "PHP CONTAINER USER $(whoami)"
echo "PHP CONTAINER Current working directory: $(pwd)"



if [ ! -f ${WORKING_DIR}/vendor/autoload.php ]; then
    echo "autoload.php not found. Setting permissions and running composer install..."
    # chown -R $(whoami):$(whoami) $(pwd)
    check_and_set_ownership "$(pwd)"
    composer update
else
    echo "vendor file found."
fi



# Wait for the MySQL container to be up 
# altough heallthcheck ensure mysql is running before php container starts, even if that is off, wait_for_mysql function can manauallly search if mysql is running.
wait_for_mysql

# Run migration status and handle error output
STATUS_OUTPUT=$(php artisan migrate:status 2>&1)
echo "the text-$STATUS_OUTPUT" 
if echo "$STATUS_OUTPUT" | grep -q "Migration table not found"; then
    echo "Migration table not found. Running migrations and seeders."
    php artisan migrate
    php artisan db:seed
else
    echo "Migration table found. Skipping migrations."
fi


# Check and set ownership of /var/www/Object-Detection-YoloV4/
check_and_set_ownership "${Object_DETECTION_FOLDER}"
# Create the resources directory if it doesn't exist
mkdir -p /var/www/Object-Detection-YoloV4/resources

# Download coco.names if it doesn't exist
if [ ! -f /var/www/Object-Detection-YoloV4/resources/coco.names ]; then
    curl -o /var/www/Object-Detection-YoloV4/resources/coco.names https://raw.githubusercontent.com/pjreddie/darknet/master/data/coco.names
else
    echo 'coco.names already exists'
fi

# Download yolov4.cfg if it doesn't exist
if [ ! -f /var/www/Object-Detection-YoloV4/resources/yolov4.cfg ]; then
    curl -o /var/www/Object-Detection-YoloV4/resources/yolov4.cfg https://raw.githubusercontent.com/AlexeyAB/darknet/master/cfg/yolov4.cfg
else
    echo 'yolov4.cfg already exists'
fi

# Download yolov4.weights if it doesn't exist
if [ ! -f /var/www/Object-Detection-YoloV4/resources/yolov4.weights ]; then
    wget -O /var/www/Object-Detection-YoloV4/resources/yolov4.weights https://github.com/AlexeyAB/darknet/releases/download/yolov4/yolov4.weights
else
    echo 'yolov4.weights already exists'
fi

echo ${pwd}
echo "Setting permission for QUEUE worker"
check_and_set_ownership "$(WORKING_DIR)"

# Start PHP-FPM in the background
php-fpm &

# Start the queue worker
while true; do
    php artisan queue:work --sleep=0 --timeout=60000
    sleep 1
done
