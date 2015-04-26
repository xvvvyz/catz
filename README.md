OMG. Catz
=========

Setup (Tested with OSX 10.10, CentOS 7 and Debian 7)
----------------------------------------------------

* Clone the repo into a place that serves HTTP requests.

```bash
git clone https://github.com/omgcatz/omgcatz /path/to/server/
```

* Install all of the things.

```bash
# Debian
sudo apt-get update
sudo apt-get install apache2 mysql-server php5 php5-mysql php-pear php5-curl curl eyeD3 atomicparsley zip file
```

* Run ./_install/setup.sh to see if you missed any installs. It will also create the Config class, create a database, and install the necessary tables for you.

* Here are a couple (optional) crontabs to delete older files.

```bash
# Remove songs that are older than 200 minutes every 5 minutes.
*/5 * * * * find /path/to/server/api/fetch/download/songs -type f -mmin +200 -delete

# Remove archives and artwork that are older than 45 minutes every 5 minutes.
*/5 * * * * find /path/to/server/api/fetch/download/{archives,artwork} -type f -mmin +45 -delete
```
If you are using slave servers, adjust accordingly.

* Fix everything that doesn't work.

* Dance.

Things to be Done
-----------------

* get play tokens from 8tracks
* always tag songs
* look into downloading huge mixes
* songza support
* fix unicode character support with tagging
* add ability to reset a fetched playlist
* make downloaded playlists page
* convert gif to png when tagging m4a (or something)
* add any apache changes to readme
* add youtube-dl support
