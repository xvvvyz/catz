OMG. Catz
=========

The latest version of omgcatz.com.

 About OMG. Catz
-----------------------------
[http://omgcatz.com/](http://omgcatz.com/)    
### **What Is This?**  
This is a cat website (made for cats... like you). With the internet came an unruley amount of cats and cat like things. Maybe the Egyptians understood something we still don't quite understand. Along with cats, this website provides you with a whole bunch of music. Music to the eyes, that is, in the form of cats.  

**Wait.. Why?** Because of this... And because obsessively viewing cat pictures while enjoying my favorite music can turn a bad day into something slightly *better*. I hope you can find joy from this website, too.
How can I thank you?You can drop me a line by email, and you can click here if you are interested in donating.

 Setup (Tested with OS X and Debain)
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

* Run setup.sh to see if you missed any installs and to create database.php. Make sure to run it again when you have everything installed.

* If you don't have unlimited disk space, you should probably put something like this in a crontab (add minutes to the find commands if you have a lot of space).

```bash
*/5 * * * * find /path/to/server/run/songs -type f -mmin +200 -delete
*/5 * * * * find /path/to/server/run/archives -type f -mmin +45 -delete
*/5 * * * * find /path/to/server/run/artwork -type f -mmin +45 -delete
```

* Create the database.

```bash
mysql -u root -p
create database lollipop;
quit
```

* Fix everything that doesn't work.

* Dance.
