# omgcatz

## about

First and foremost, the goal of this project is to provide the user with an abundance of feline imagery. Which it currently isn't very good at. However, it is capable of downloading music and other media from the interwebz that is otherwise hard to acquire.

### supported sites

* [8tracks.com](https://8tracks.com/)
* [songza.com](https://songza.com/) (in development)

## developer setup

### prerequisites

1. [VirtualBox](https://www.virtualbox.org/)
2. [Vagrant](https://www.vagrantup.com/)

### install necessary vagrant plugins

```bash
vagrant plugin install vagrant-vbguest
```

### get the omgcatz stuff

```bash
git clone https://github.com/cadejscroggins/omgcatz && cd omgcatz
```

### initialize the virtual machine

```bash
vagrant up
```

### run the omgcatz setup script

```bash
vagrant ssh -c "/var/www/html/_install/setup.sh"
```

The MySQL username is `root` and the password is `vagrant`.

### that's it!

You can access the site at [http://localhost:8080](http://localhost:8080).

If you run into any issues, feel free to submit them on GitHub. On that note, if you want to contribute, pull requests are always welcome. (:
