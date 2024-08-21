#!/bin/sh
# set -e

# THIS SCRIPT WORKS WHEN CONTAINER is UP or UP COMMAND IS RUN
#installation post work, executed from outside the directory

# Function to handle cleanup on termination - Stopping Script
cleanup() {
    echo "Container is stopping... Running cleanup tasks..."
    # Add your cleanup commands here
    # For example: stopping services, saving state, etc.
    echo "Cleanup complete."
    exit 0
}

settingUpLaravelFilePermissions() {
    chmod 644 /var/www/public_html/.env
    find /var/www/public_html -type f -exec chmod 644 {} \;  
    find /var/www/public_html -type d -exec chmod 755 {} \;
    chown -R www-data:www-data /var/www/public_html/storage/ /var/www/public_html/bootstrap/cache/
    chmod -R 775 /var/www/public_html/storage/ /var/www/public_html/bootstrap/cache/
}

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

# Trap SIGTERM and SIGINT signals and run the cleanup function
trap 'cleanup' SIGTERM SIGINT
settingUpLaravelFilePermissions

echo "PHP CONTAINER USER $(whoami)"
echo "PHP CONTAINER Current working directory: $(pwd)"

echo "\nEnsuring all composer libraries are loaded..."
if [ ! -f ${WORKING_DIR}/vendor/autoload.php ]; then
    echo "autoload.php not found. Setting permissions and running composer install..."
    check_and_set_ownership "/var/www/.composer/cache/repo/https---repo.packagist.org/"
    check_and_set_ownership "/var/www/.composer/cache/files/"
    check_and_set_ownership "${WORKING_DIR}/vendor/"
    check_and_set_ownership "${WORKING_DIR}/composer.lock"
    composer update    
else
    echo "vendor file found."
fi

check_and_set_ownership "bootstrap/cache"

# if [ ! -f "/var/www/.npm" ]; then
if [ ! -d "${WORKING_DIR}/node_modules" ] || [ -z "$(ls -A ${WORKING_DIR}/node_modules | grep -v '.gitignore')" ]; then
    echo "node_modules directory is missing or empty. Setting permissions and running npm install..."
    # echo ".npm directory not found. Setting permissions and running npm install..."
    

    check_and_set_ownership "/var/www/.npm"
    check_and_set_ownership "/var/www/.npm/_locks"
    check_and_set_ownership "/var/www/.config"
    check_and_set_ownership "${WORKING_DIR}/node_modules/"
    # chown -R www-data:www-data /var/www/public_html/node_modules/
    chmod -R 755 "${WORKING_DIR}/node_modules/"
    npm install
    npm run dev
else
    echo ".npm directory found."
fi



echo "\nEnsuring all database tables exists..."
# Wait for the MySQL container to be up 
# altough heallthcheck ensure mysql is running before php container starts, even if that is off, wait_for_mysql function can manauallly search if mysql is running.
wait_for_mysql

# Run migration status and handle error output
STATUS_OUTPUT=$(php artisan migrate:status 2>&1)
echo "the text-$STATUS_OUTPUT" 
if echo "$STATUS_OUTPUT" | grep -q "Migration table not found"; then
    echo "Migration table not found. Running migrations and seeders."
    php artisan migrate
    # php artisan laratrust:setup
    # php artisan vendor:publish --tag="laratrust"
    # php artisan vendor:publish --tag=laratrust-assets --force
    # php artisan vendor:publish --tag=laratrust-assets
    php artisan db:seed
else
    echo "Migration table found. Skipping migrations."
fi


echo "\nEnsuring files for Object Detection..."
check_and_set_ownership "${Object_DETECTION_FOLDER}/io"
check_and_set_ownership "${Object_DETECTION_FOLDER}/resources"

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


echo "\nSetting permission for QUEUE worker..."
check_and_set_ownership "${WORKING_DIR}/storage/"

echo "\nEnsuring storage directory link exists for file uploads..."
if [ ! -L "${WORKING_DIR}/public/storage" ]; then
    echo "Storage link not found. Setting up storage link..."
    check_and_set_ownership "${WORKING_DIR}/public"
    php artisan storage:link
else
    echo "Storage link already exists."
fi


echo "\n"

# Start PHP-FPM in the background
php-fpm  &

# Start the queue worker
while true; do
    php artisan queue:work --sleep=0 --timeout=60000
    sleep 1
done