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
sudo apt-get install apache2 mysql-server php5 php5-mysql php-pear php5-curl curl eyeD3 atomicparsley zip file
sudo /etc/init.d/apache2 restart
```

* Run ./install/setup.sh to see if you missed any installs. It will also create the DatabaseLogin class, create a database, and install the necessary tables for you.

* If you don't have unlimited disk space, you should probably put something like this in a crontab (add minutes to the find commands if you have a lot of space).

```bash
# Remove songs that are older than 200 minutes every 5 minutes.
*/5 * * * * find /path/to/server/api/download/songs -type f -mmin +200 -delete

# Remove archives and artwork that are older than 45 minutes every 5 minutes.
*/5 * * * * find /path/to/server/api/download/{archives,artwork} -type f -mmin +45 -delete
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
