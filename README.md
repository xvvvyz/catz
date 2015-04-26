Catz
====

Setup Guide
----------------

Clone the repo into a place that serves HTTP requests.

```bash
git clone https://github.com/omgcatz/omgcatz /path/to/server/
```

Install all of the things.

```bash
# Debian
apt-get install apache2 mysql-server php5 php5-mysql php5-curl curl eyeD3 atomicparsley imagemagick zip file
```

Run ./_install/setup.sh to see if you missed any installs. It will also create the Config class, create a database, and install the necessary tables for you.

Here are a couple (optional) crontabs to delete older downloads.

```bash
# Remove songs that are older than 200 minutes every 5 minutes.
*/5 * * * * find /path/to/songs -type f -mmin +200 -delete

# Remove archives and artwork that are older than 45 minutes every 5 minutes.
*/5 * * * * find /path/to/{archives,artwork} -type f -mmin +45 -delete
```

Things to be Done
-----------------

* look into downloading huge mixes
* add any apache/php/mysql changes to readme
* songza support
* reset playlist when trackcount changes
* add soundcloud
* make downloaded playlists page
* add youtube-dl support
* fix unicode character support with tagging
* mark mix as done when done (even if total tracks don't match)
