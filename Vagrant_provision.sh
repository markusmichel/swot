#!/usr/bin/env bash
###########################################################
#
# Provisioning script for the Vagrant VM
#
# This script is run by Vagrant, based on the Vagrantfile.
# It sets up the Auf Haxe development server.
#
# Check vagrantup.com for general information about Vagrant
# or https://aufhaxe.de/wiki/entwicklung:setup for a guide.
#
###########################################################

# Add better mirror selection for apt
if ! grep -q mirrors /etc/apt/sources.list; then
	echo 'deb mirror://mirrors.ubuntu.com/mirrors.txt precise main restricted universe multiverse' | cat - /etc/apt/sources.list > temp && sudo mv temp /etc/apt/sources.list
	echo 'deb mirror://mirrors.ubuntu.com/mirrors.txt precise-updates main restricted universe multiverse' | cat - /etc/apt/sources.list > temp && sudo mv temp /etc/apt/sources.list
fi

# Only update package list if the last update is more than 2 weeks ago (or the cache file does not exist yet)
if [[ $(expr $(date +%s) - $(stat -c %Y /var/cache/apt/pkgcache.bin)) -ge 1209600 || ! -f /var/cache/apt/pkgcache.bin ]]; then
	sudo apt-get update
fi


# Prepare the MySQL password (before installation)
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'


# Install required packages
sudo apt-get install -q -y --no-upgrade \
	apache2 \
	libapache2-mod-php5 \
	php5-mysql \
	mysql-server \
	mysql-client \
	php5-gd \
	php5-curl \
	poppler-utils \
	firefox=11.0+build1-0ubuntu4 \
	xvfb \
	openjdk-7-jre-headless \
	gettext \
	make

# Delete default page of Apache
sudo rm -f /var/www/index.html


# install PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version


# Make htaccess files work
#sudo sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/sites-available/default
#sudo a2enmod rewrite
sudo service apache2 reload


cd /var/www
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
