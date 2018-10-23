# portifolioacompanhator
Simple scrips to plot graphs of your cryptocurrencies portfolio

#instalation
## Install pre-requisites:
* MySQL
* Apache
* PHP
* jq
* curl
* bash

## Create MySQL DB and users:
```
bash$ sudo mysql
mysql> create database quotes;
mysql> create user 'username'@'localhost' identified by 'password';
mysql> grant all privileges on quotes.* TO 'username'@'localhost';
```

## Edit the database connection info:

* Rename file quotes/db_config.sh.sample to quotes/db_config.sh
* Edit quotes/db_config.sh to contein the correct username and password for the user you created

## Add the quotes.sh scripts to yor cron. Mine looks like:
# quotes
```
0 */1 * * * /home/girino/git/portifolioacompanhator/cron/quotes.sh
```

Your script is now collecting data. You need not to install the php files in order to display graphs.

## Enable PHP on your apache server. On ubuntu 16.04 all i did was:
```
bash$ sudo apt-get install php libapache2-mod-php php-mcrypt php-mysql
bash$ sudo phpenmod pdo_mysql
bash$ sudo service apache2 restart 
```

## Install PHP files
* Copy www folder contents to /var/www/html or some subfolder (I use /var/www/html/quotes). If you are using some other linusx distro, check the correct folder where apache serves files.

## OPTIONAL: secure the folder with a password.
I provided a sample .htacess file. But you should check your distros documentation on how to best passowrd secure an apache folder.

# Enjoy!
