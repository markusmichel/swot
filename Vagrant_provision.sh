#!/usr/bin/env bash

sudo apt-get update

# Prepare the MySQL password (before installation)
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

# Add latest version of php
sudo apt-get update
sudo apt-get install python-software-properties build-essential -y
sudo add-apt-repository ppa:ondrej/php5 -y
sudo apt-get update

# Install required packages
sudo apt-get install -q -y --no-upgrade \
	apache2 \
	curl \
	libapache2-mod-php5 \
	php5 \
	php5-mysql \
	php5-curl \
	mysql-server \
	mysql-client \
	php5-gd \
	php5-curl \
	php5-xdebug \
	poppler-utils \
	xvfb \
	openjdk-7-jre-headless \
	gettext \
	make

# Delete default page of Apache
sudo rm -f /var/www/index.html


# XDebug configuration
echo -e "zend_extension=xdebug.so \nxdebug.remote_enable = on \nxdebug.remote_connect_back = on \nxdebug.idekey = \"vagrant\"" | sudo tee /etc/php5/mods-available/xdebug.ini


# Copy tmp vhost file with sudo
sudo cp /tmp/000-default.conf /etc/apache2/sites-available/000-default.conf
sudo service apache2 reload


# install PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version


# If phpmyadmin does not exist
if [ ! -f /etc/phpmyadmin/config.inc.php ];
then

	# Used debconf-get-selections to find out what questions will be asked
	# This command needs debconf-utils

	# Handy for debugging. clear answers phpmyadmin: echo PURGE | debconf-communicate phpmyadmin

	echo 'phpmyadmin phpmyadmin/dbconfig-install boolean false' | debconf-set-selections
	echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections

	echo 'phpmyadmin phpmyadmin/app-password-confirm password root' | debconf-set-selections
	echo 'phpmyadmin phpmyadmin/mysql/admin-pass password root' | debconf-set-selections
	echo 'phpmyadmin phpmyadmin/password-confirm password root' | debconf-set-selections
	echo 'phpmyadmin phpmyadmin/setup-password password root' | debconf-set-selections
	echo 'phpmyadmin phpmyadmin/database-type select mysql' | debconf-set-selections
	echo 'phpmyadmin phpmyadmin/mysql/app-pass password root' | debconf-set-selections

	echo 'dbconfig-common dbconfig-common/mysql/app-pass password root' | debconf-set-selections
	echo 'dbconfig-common dbconfig-common/mysql/app-pass password' | debconf-set-selections
	echo 'dbconfig-common dbconfig-common/password-confirm password root' | debconf-set-selections
	echo 'dbconfig-common dbconfig-common/app-password-confirm password root' | debconf-set-selections
	echo 'dbconfig-common dbconfig-common/app-password-confirm password root' | debconf-set-selections
	echo 'dbconfig-common dbconfig-common/password-confirm password root' | debconf-set-selections

	apt-get -y install phpmyadmin
fi

sudo ln -sf /usr/share/phpmyadmin/ /var/www/web/phpmyadmin


# Make htaccess files work
sudo sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/sites-available/default
sudo a2enmod rewrite
sudo service apache2 reload


cd /var/www
curl -sS https://getcomposer.org/installer | php
php composer.phar install
php app/console doctrine:database:create
php app/console doctrine:schema:update --force