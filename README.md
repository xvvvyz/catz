OMG. Catz Setup Guide
=====================

This guide will (hopefully) get you close to having Catz run on your local or remote machine.

Prereqs
-------

A *NIX box. (Tested with OS X Mavericks and Debain).

Setup
-----

0. Clone the repo into a place that serves http requests.

`git clone https://github.com/cadejscroggins/omgcatz /path/to/server/`

1. Install all of the things (Debian)

`sudo apt-get update
sudo apt-get install apache2
sudo apt-get install mysql-server
sudo apt-get install php5
sudo apt-get install php5-mysql
sudo apt-get install php-pear
sudo apt-get install php5-suhosin
sudo apt-get install php5-curl
sudo apt-get install curl
sudo apt-get install eyeD3
sudo apt-get install atomicparsley
sudo apt-get install p7zip
sudo /etc/init.d/apache2 restart`

2. Run setup.sh to see if you missed any installs and to create database.php. Make sure to run it again when you have everything installed.

3. If you don't have unlimited disk space, you should probably put something like this in a crontab.

`*/5 * * * * find /path/to/server/run/songs -type f -mmin +200 -delete
*/5 * * * * find /path/to/server/run/archives -type f -mmin +45 -delete
*/5 * * * * find /path/to/server/run/artwork -type f -mmin +45 -delete`

4. Create the database.

`mysql -u root -p
create database lollipop;
quit`

5. Fix everything that doesn't work.

6. Dance.
