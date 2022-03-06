Refactoring Folders

docs
features
io
downloadedable_resources

### Install
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
generate db
php artisan migrate:fresh --seed
npm install
npm run dev
php artisan serve --host=0.0.0.0
php artisan queue:work --sleep=0 --timeout 60000

### 	Package Requirements
- Php requirement : 7
- Laravel Framework 8.68.1
- Existing Spatie/MediaLibrary: "^7.19"

  ### Software Requirements

- Composer

- Nodejs

- Xampp Windows - Apache, Mysql, php

- Wammp Ubuntu - Apache, mysql, php, phpmyadmin

  ### Developer Environment

  ​				**Windows**

- Install visual code

- Visual Studio Code Extensions

  - PHP IntelliSense
  - PHP Intelephense  		
  - PHP DocBlocker		
  - PHP Namespace Resolver
  - Laravel goto view
  - Laravel Blade Snippets
  - phpfmt
  - Bracket Pair Colorizer
  - HTML Snippets
  - Laravel Model Snippets

- Font
  https://itsfoss.com/install-fonts-ubuntu/
  https://github.com/tonsky/FiraCode/wiki/VS-Code-Instructions
  https://tahoeninjas.blog/2019/03/16/setting-fira-code-as-your-default-visual-studio-code-font/
  https://itsfoss.com/hide-folders-and-show-hidden-files-in-ubuntu-beginner-trick/

    		Fira Code
    	cd ~
    	mkdir .fonts
    	ls -a
    	go to fira code extracted directory 
    	cp * ~/.fonts


- Chrome Extension
  	vuejs-devtools - Find vue object in developer tools



### Quick Setup

#### Installing APPS

##### Visual Code

https://code.visualstudio.com/download
download 64 bit .deb and double click
install it in ubuntu software

##### Typora

> wget -qO - https://typora.io/linux/public-key.asc | sudo apt-key add -

###### add Typora's repository

> sudo add-apt-repository 'deb https://typora.io/linux ./'
> sudo apt-get update

###### install typora

> sudo apt-get install typora

##### Install GIT

Git – sudo apt install git

##### Install Composer

sudo apt install composer

##### Install NPM

> sudo apt update
> sudo apt install nodejs npm
> nodejs --version
> v10.19.0

#### Setup LAMP

##### INSTALL Apache2

> sudo apt update
> sudo apt install apache2

##### Enabling through firewall

> sudo ufw app list
> sudo ufw allow in "Apache"
> sudo ufw enable
> sudo ufw status

##### Installing mysql, php and php myadmin

> sudo apt install mysql-server
> sudo apt update
> sudo apt install php-mbstring php-zip php-gd php-json php-curl
> sudo apt install phpmyadmin 

For the server selection, choose apache2 Warning: When the prompt appears, “apache2” is highlighted, but not selected. If you do not hit SPACE to select Apache, the installer will not move the necessary files during installation. 

>>> Hit SPACE, TAB, and then ENTER to select Apache. 
>>> • Select Yes when asked whether to use dbconfig-common to set up the database 
>>> • >>> you can keep blank or  You will then be asked to choose and confirm a MySQL application password for phpMyAdmin  - I left empty for now and enter

https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04#step-2-%E2%80%94-installing-mysql

> sudo mysql_secure_installation
> y and enter
> give password rool 2 or strong
> %Sh170892 and enter
> paste again and enter
> y and enter
> y and enter
> y and enter
> y and enter
> y and enter

##### Restart System

sudo service apache2 restart
sudo service mysql restart


###### This will ask if you want to configure the VALIDATE PASSWORD PLUGIN.

Note: Enabling this feature is something of a judgment call. If enabled, passwords which don’t match the specified criteria will be rejected by MySQL with an error. It is safe to leave validation disabled, but you should always use strong, unique passwords for database credentials.

###### Please set the password for root here. And confirm


###### For the rest of the questions, press Y and hit the ENTER key at each prompt. This will remove some anonymous users and the test database, disable remote root logins, and load these new rules so that MySQL immediately respects the changes you have made.

##### Creating login user for phpmyadmin

> sudo mysql
> exit
> sudo apt install php libapache2-mod-php php-mysql
> y and enter
> php -v
> PHP 7.4.3 (cli) (built: Oct 25 2021 18:20:54) ( NTS )
> Copyright (c) The PHP Group
> Zend Engine v3.4.0, Copyright (c) Zend Technologies
> with Zend OPcache v7.4.3, Copyright (c), by Zend Technologies


http://localhost/phpmyadmin/index.php
mysqli_real_connect(): (HY000/1698): Access denied for user 'root'@'localhost'

> sudo service mysql restart

Log into MYSQL as root - :

> sudo mysql -u root

Grant privileges. For a new user, execute:

> CREATE USER 'shaon'@'localhost' IDENTIFIED BY '%Sh170892';

# use special character and at least one cap lock character if policy doesnt grant easy password

> GRANT ALL PRIVILEGES ON *.* TO 'shaon'@'localhost';
> FLUSH PRIVILEGES;
> exit

phpmyadmin  the file size exceeded the maximum size permitted by your PHP configuration
---

link - https://serverfault.com/questions/1060449/file-size-exceeded-the-maximum-size-permitted-phpmyadmin
---

my php version is 

> php -v
> Result :
> PHP 7.4.3 (cli) (built: Aug 13 2021 05:39:12) ( NTS )
> Copyright (c) The PHP Group
> Zend Engine v3.4.0, Copyright (c) Zend Technologies
> with Zend OPcache v7.4.3, Copyright (c), by Zend Technologies

##### Solving upload problem

Find the php ini file
-
cd /etc/php/7.4/apache2
sudo nano php.ini 
or,
code php.ini

then after opening change 
upload_max_filesize = 2M
I change to 5M as required as my importing mysql file size.
then I saved as sudo

restart apache2-
sudo service apache2 restart

then you can create db with desire collation, in my cased utf8mb4_unicode_ci and import your database 



#### Adding to Repo

##### For Github tokens - https://github.com/settings/tokens

Error: Could not get lock /var/lib/apt/lists/lock.
Explanation : Ubuntu Software or Terminal using the apt manager to get package or remove or updating

> git init
> git add .
> git commit -m "SabitaMLTube"
> git config user.emal "Shaon Majumder"
> git config user.email "smazoomder@gmail.com"
> git remote add origin https://github.com/ShaonMajumder/SABITA-A-MLTube.git
> git remote set-url origin https://github.com/ShaonMajumder/SABITA-A-MLTube.git
> git push origin master


1. open git bash

2. Cloning The Repository >
   `git clone https://github.com/ShaonMajumder/SABITA-A-MLTube.git`

3. **[Optional]** Go to repo folder >

   `cd SABITA-A-MLTube`

4. **[Optional]** Set project user  >

   `git config user.name "Shaon Majumder"`

5. **[Optional]** Set project email >

   ` git config user.email "smazoomder@gmail.com"`

6. Go to Laravel Project Directory of the Repository >
   `cd dev`

7. Open Code and Open project folder SABITA-A-MLTube

8. ctrl + shift + ~ for bash

9. open dev
   `cd dev`

10. Generate the environment file >
       `cp .env.example .env`

11. Open env
       `code .env`
    . **[Optional]** Edit the environment file >
       `nano .env`

    Editing Fields - 
    
    APP_NAME="সবিতা"
    
    APP_ENV=local
    
    APP_KEY=base64:FKYPM6nRaQateotna9Za4FFpW09ZhQ8NlfbQ7MFHbK8=
    
    APP_DEBUG=true
    
    APP_URL=http://127.0.0.1:8000
    
    
    
    LOG_CHANNEL=stack
    
    LOG_LEVEL=debug
    
    
    
    DB_CONNECTION=mysql
    
    DB_HOST=127.0.0.1
    
    DB_PORT=3306
    
    DB_DATABASE=sabita
    
    DB_USERNAME=shaon
    
    DB_PASSWORD=%Sh170892
    
    
    
    BROADCAST_DRIVER=log
    
    CACHE_DRIVER=file
    
    QUEUE_CONNECTION=database
    
    SESSION_DRIVER=file
    
    SESSION_LIFETIME=120
    
    
    
    MEMCACHED_HOST=127.0.0.1
    
    
    
    REDIS_HOST=127.0.0.1
    
    REDIS_PASSWORD=null
    
    REDIS_PORT=6379
    
    
    
    MAIL_MAILER=smtp
    
    MAIL_HOST=smtp.gmail.com
    
    MAIL_PORT=587
    
    MAIL_USERNAME=secret@gmail.com
    
    MAIL_PASSWORD=secret
    
    MAIL_ENCRYPTION=tls
    
    MAIL_FROM_ADDRESS=secret@gmail.com
    
    MAIL_FROM_NAME="${APP_NAME}"
    
    
    
    AWS_ACCESS_KEY_ID=
    
    AWS_SECRET_ACCESS_KEY=
    
    AWS_DEFAULT_REGION=us-east-1
    
    AWS_BUCKET=
    
    
    
    PUSHER_APP_ID=
    
    PUSHER_APP_KEY=
    
    PUSHER_APP_SECRET=
    
    PUSHER_APP_CLUSTER=mt1
    
    
    
    MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    
    MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    
    
    
12. Installing composer packages >
       `composer install`
       then
       `composer update`
       if problem occurs - 
       `composer clearcache`
       if previously not installed
       `composer install`
       then
       `composer update`
       ​	

13. Installing npm packages >
       `npm install`
    
    if error try second time

   ​	use if finding folder is needed - npm list -g
   ​	if error use npm install --global cross-env
   `npm run dev`

14. Generating Application Key >
     `php artisan key:generate`

15. Generating Symbolic link >
     `php artisan storage:link`
     Output: 
     The system cannot find the file specified. If error then generate again.
     The [D:\Projects\Banglatube\Working\Banglatube\public\storage] link has been connected to [D:\Projects\Banglatube\Working\Banglatube\storage\app/public].
     The links have been created.

      Linux :
    
    The [/windows/ROBIST/Projects/SABITA-A-MLTube/dev/public/storage] link has been connected to [/windows/ROBIST/Projects/SABITA-A-MLTube/dev/storage/app/public].
    The links have been created.
    
    Office:
    
    The [/home/shaon/Projects/SABITA-A-MLTube/dev/public/storage] link has been connected to [/home/shaon/Projects/SABITA-A-MLTube/dev/storage/app/public].
    The links have been created.
    
16. Start Xampp/LAMP APPACHE, MYSQL
    `sudo service apache2 restart`
    `sudo service mysql restart`

17. Create the database mentioned in the .env file
     http://localhost/phpmyadmin
     create db 'sabita' and collation - utf-8-general_ci

18. `php artisan migrate:fresh`

19. `php artisan db:seed`
      or, 15+16 `php artisan migrate:fresh --seed`

20. HOST
    `php artisan serve`

21. Check the url. In our case - http://127.0.0.1:8000/

22. ## start

     Queue Jobs >

    `php artisan queue:work --sleep=0 --timeout 60000`

    with sudo or without sudo

    if erros -
    `php artisan --version`

    Result : if port is ok at .env then no sudo required 

23. Login with 
    "email"=> smazoomder@gmail.com
    "password" => laravel
    
24. change back ownership of a file to current user -

    sudo chown shaon  README.md 
    sudo chown shaon -R problems

25. ###### vhost

    `code /etc/hosts`
    127.0.1.1	sabita.test
    and save

`cd /etc/apache2/sites-available`
`sudo cp 000-default.conf sabita.test.conf`
code sabita.test.conf
<VirtualHost *:80>

	# The ServerName directive sets the request scheme, hostname and port that
	
	# the server uses to identify itself. This is used when creating
	
	# redirection URLs. In the context of virtual hosts, the ServerName
	
	# specifies what hostname must appear in the request's Host: header to
	
	# match this virtual host. For the default virtual host (this file) this
	
	# value is not decisive as it is used as a last resort host regardless.
	
	# However, you must set it for any further virtual host explicitly.

​	#ServerName www.example.com

	ServerAdmin admin@sabita.test
	ServerName sabita.test
	DocumentRoot /windows/ROBIST/Projects/SABITA-A-MLTube/dev/public/
	DirectoryIndex index.php
	<Directory /windows/ROBIST/Projects/SABITA-A-MLTube/dev/public/>
		Options -Indexes +FollowSymLinks +MultiViews
		AllowOverride All
		Require all granted
		<FilesMatch \.php$>
		# Change this "proxy:unix:/path/to/fpm.socket"
		# if using a Unix socket
		#SetHandler "proxy:fcgi://127.0.0.1:9000"
		</FilesMatch>
	</Directory>
	
	# Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	# error, crit, alert, emerg.
	# It is also possible to configure the loglevel for particular
	# modules, e.g.
	#LogLevel info ssl:warn
	
	ErrorLog ${APACHE_LOG_DIR}/sabita.test-error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn
	
	# For most configuration files from conf-available/, which are
	# enabled or disabled at a global level, it is possible to
	# include a line for only one particular virtual host. For example the
	# following line enables the CGI configuration for this host only
	# after it has been globally disabled with "a2disconf".
	#Include conf-available/serve-cgi-bin.conf

</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet

and save



`sudo a2dissite 000-default.conf`
`sudo a2ensite sabita.test.conf`
`sudo service apache2 restart`

26. Change in APP_URL=http://sabita.test

and check http://sabita.test

27. UnexpectedValueException

​    The stream or file  "/home/shaon/Projects/SABITA-A-MLTube/dev/storage/logs/laravel.log"  could not be opened in append mode: failed to open stream: Permission  denied

get octal file permissions -

```bash
stat -c "%a %n" *
```

see owner of ''storage'' directory -

>  ls -l

change it to apache2 server default running user www-data 

>  sudo chown www-data -R ./storage

Solved

27. php artisan queue:work --sleep=0 --timeout 60000 

    UnexpectedValueException 

      The stream or file "/home/shaon/Projects/SABITA-A-MLTube/dev/storage/logs/laravel.log" could not be opened in append mode: failed to open stream: Permission denied

    needs sudo then.

    how to run without sudo

    >  cd ~/Projects/SABITA-A-MLTube/dev/storage/logs
    
    >  ls -l

if group owner is set to root , change to www-data -

>  sudo chgrp www-data laravel.log

28. On image upload -

    ErrorException

​     unlink(/home/shaon/Projects/SABITA-A-MLTube/dev/storage/app/public/3/conversions/245220355_2067662436717766_8079095366020228340_n-thumb.jpg): Permission denied

29. This may occur if you are using virtual host.

> php artisan queue:work --sleep=0 --timeout 60000
> Processing: Spatie\MediaLibrary\Jobs\PerformConversions
> Failed:     Spatie\MediaLibrary\Jobs\PerformConversions

   UnexpectedValueException :

  The stream or file "/home/shaon/Projects/SABITA-A-MLTube/dev/storage/logs/laravel.log" could not be opened in append mode: failed to open stream: Permission denied

Solution: follow 27 but change the command to chown. means change the owner of the laravel.log file to current user

30. On image upload 

    > php artisan queue:work --sleep=0 --timeout 60000
    > [2021-11-04 04:34:31][5] Processing: Spatie\MediaLibrary\Jobs\PerformConversions
    > [2021-11-04 04:34:31][5] Failed:     Spatie\MediaLibrary\Jobs\PerformConversions

cd ~/Projects/SABITA-A-MLTube/dev/storage/app

ls -l

may be public folder is locked

change to current user if  www-data is current user, or change to www-data

> sudo chown shaon public/

31. UnexpectedValueException

    The stream or file "/home/shaon/Projects/SABITA-A-MLTube/dev/storage/logs/laravel.log" could not be opened in append mode: failed to open stream: Permission denied

    > ls -l /home/shaon/Projects/SABITA-A-MLTube/dev/storage/logs/laravel.log
    >
    > sudo chown shaon  /home/shaon/Projects/SABITA-A-MLTube/dev/storage/logs/laravel.log

32. League\Flysystem\Exception

Impossible to create the root directory "/home/shaon/Projects/SABITA-A-MLTube/dev/storage/app/public/12".

For checking permission

> ls -l storage 

set the owner to current user 

> sudo chown -R shaon  storage

set the group to www-data

> sudo chgrp -R www-data  storage

then check again the permission

> ls -l storage

Note: All file will be set to owner at current user and group www-data

If not gone, check

Finding out what user Apache is running as -

>  ps aux | egrep '(apache|httpd)'

A list will be shown

stop apache2 - 

>  sudo service apache2 stop

Then check again -

> ps aux | egrep '(apache|httpd)'

> sudo service apache2 start

see file permission on octal

```bash
stat -c "%a %n" storage/
```

ErrorException

unlink(/home/shaon/Projects/SABITA-A-MLTube/dev/storage/app/public/27/245220355_2067662436717766_8079095366020228340_n.jpg): Permission denied



Last Install previously installed vhost and host and enable conf:

git config user.name "Shaon Majumder"
git config user.email "smazoomder@gmail.com"
cd dev
cp .env.example .env
code .env
composer install
composer update
npm install
npm run dev
php artisan key:generate
php artisan storage:link
sudo service apache2 restart
sudo service mysql restart
php artisan migrate:fresh --seed
php artisan serve --host=0.0.0.0
Linux :: sudo apt  install ffmpeg
For Windows :: download ffmpeg and link
php artisan queue:work --sleep=0 --timeout 60000

cd ..

sudo chown shaon  README.md 
sudo chown shaon -R problems/

cd dev

##### Check 

image upload check --- running ----





##### 

------------ sure till this --------------

1. 

##### Check 

image upload check

video upload check

##### Permission

```php
sudo chmod -R 777 ./storage
sudo chmod -R 777 ./bootstrap/cache/
```

##### DEV

1. Changing name .env
   APP_NAME="সবিতা"
   https://lipighor.com/Mayukh.html
   webpack.mix.js > add >
     mix.copy('resources/fonts', 'public/fonts');
   place the font in resources/fonts

add the lines in any css file :
@font-face {
  font-family: Li-Mayukh-Unicode;
  src: url('/fonts/Li-Mayukh-Unicode.ttf');
}

#navbar_icon{
  font-family: Li-Mayukh-Unicode;
  color: red;
}

npm install
npm run dev
clear chache and reload

set custom font laravel 8 - https://stackoverflow.com/questions/68885763/importing-custom-fonts-in-laravel-8

## Troubleshoot not tested

composer remove "spatie/laravel-medialibrary:^7.0.0"

composer require "spatie/laravel-medialibrary:^7.0.0"

php artisan vendor:publish

output:

Which provider or tag's files would you like to publish?:
  [0 ] Publish files from all providers and tags listed below
  [1 ] Provider: Facade\Ignition\IgnitionServiceProvider
  [2 ] Provider: Fideloper\Proxy\TrustedProxyServiceProvider
  [3 ] Provider: Fruitcake\Cors\CorsServiceProvider
  [4 ] Provider: Illuminate\Foundation\Providers\FoundationServiceProvider
  [5 ] Provider: Illuminate\Mail\MailServiceProvider
  [6 ] Provider: Illuminate\Notifications\NotificationServiceProvider
  [7 ] Provider: Illuminate\Pagination\PaginationServiceProvider
  [8 ] Provider: Intervention\Image\ImageServiceProviderLaravelRecent
  [9 ] Provider: Laravel\Tinker\TinkerServiceProvider
  [10] Provider: ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider
  [11] Provider: Spatie\MediaLibrary\MediaLibraryServiceProvider
  [12] Tag: config
  [13] Tag: cors
  [14] Tag: flare-config
  [15] Tag: ignition-config
  [16] Tag: laravel-errors
  [17] Tag: laravel-mail
  [18] Tag: laravel-notifications
  [19] Tag: laravel-pagination
  [20] Tag: migrations
  [21] Tag: views

 > 11

Copied File [/vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub] To [/database/migrations/2021_10_31_065833_create_media_table.php]
Copied Directory [/vendor/spatie/laravel-medialibrary/resources/views] To [/resources/views/vendor/medialibrary]
Publishing complete.



```bash
sudo chmod -R 777 storage/
```

sudo chgrp -R www-data dev/

sudo chown -R www-data dev/

 sudo chmod -R 775 dev/

----------- sure till this -----------

17. **[Optional]** VHOST FILE : D:\Projects\Banglatube\docs\Windows\httpd-vhosts.conf > 

      1. Comment Everything with #

      2. add this lines

         <VirtualHost *:80>

           ServerName banglatube.test

           DocumentRoot "D:\Projects\Banglatube\Working\Banglatube\public"

           <Directory "D:\Projects\Banglatube\Working\Banglatube\public">

         ​    Options Indexes FollowSymLinks

         ​    AllowOverride all

         ​    Require all granted

           </Directory>

         </VirtualHost>

18. **[Optional]**  hosts file - C:\Windows\System32\drivers\etc\hosts open with administrative permission >

      Add this line to the file -    127.0.0.1       banglatube.test

19. **[Optional]** Restart Xampp

### Run

1. Run npm >

`npm run watch`

2. Queue Jobs >

`php artisan queue:work --sleep=0 --timeout 60000`

### Project Details

**Feature Details**
Pages -

	 1. Home Page > Search
	 2. User Channel Page> Changing Profile Pic{Pic Resizing}, Avatar[Not implemented], Name, Description, Subscriber Count, Video List, Views[not done]
	 3. Upload Video Page> Video Uploading, Video Uploading Progress, Uploading Progress, Thumbnail Generation, Video Processing/Conversion Progress, Video Link, Done
	 4. Video Watch Page > Video m3u8 player with quality according to watching user network, Views count, voting, comment
	 5. Search Page
	 6. Brand Page/Seller Page [Suggested]
	 7. Verification Tick [Suggested]

Systems - 

1. Voting System > Like/Dislike [Polymorphic],  

2. Commenting System > sub voting enabled [Polymorphic],  

3. Content Searching System > Video, Channel,  
   	Across everywhere{comment, page, ...} on the site[suggested]  

4. Uploading System  
5. Subscription System

