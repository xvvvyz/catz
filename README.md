OMG. Catz
=========

The latest (broken) version of omgcatz.com.

Setup (Tested with OSX 10.9 and Debain 7)
---------------------------------------------

* Clone the repo into a place that serves HTTP requests.

```bash
git clone https://github.com/cadejscroggins/omgcatz /path/to/server/
```

* Install all of the things (Debian).

```bash
sudo apt-get update
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
sudo apt-get install zip
sudo /etc/init.d/apache2 restart
```

* Run ./install/setup.sh to see if you missed any installs. It will also create the DatabaseLogin class, create a database, and install the necessary tables for you.

* If you don't have unlimited disk space, you should probably put something like this in a crontab (add minutes to the find commands if you have a lot of space).

```bash
*/5 * * * * find /path/to/server/run/songs -type f -mmin +200 -delete
*/5 * * * * find /path/to/server/run/archives -type f -mmin +45 -delete
*/5 * * * * find /path/to/server/run/artwork -type f -mmin +45 -delete
```

* Fix everything that doesn't work.

* Dance.

Things to be Done
-----------------

* refactor and clean up php
* refactor and clean up js
* use a task manager (probably grunt) for tasks
* clean css and start using sass
* enable downloading between multiple slave servers
* add youtube-dl support
* add ability to reset a fetched playlist
* make downloaded playlists page
* convert gif to png when tagging m4a (or something)
