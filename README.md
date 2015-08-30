# omgcatz

First and foremost, the goal of this project is to provide the user with an abundance of feline imagery. Which it currently isn't very good at. However, it is capable of downloading music from the interwebz that is otherwise hard to acquire.

#### Supported sites:

* [8tracks.com](https://8tracks.com/)

#### In development:

* [songza.com](https://songza.com/)

## Developer Setup

#### Prerequisites:

1. [VirtualBox](https://www.virtualbox.org/)
2. [Vagrant](https://www.vagrantup.com/)

#### Install necessary vagrant plugins:

```bash
vagrant plugin install vagrant-vbguest
```

#### Get the omgcatz stuff:

```bash
git clone https://github.com/cadejscroggins/omgcatz && cd omgcatz
```

#### Initialize the virtual machine:

```bash
vagrant up
```

#### Run the omgcatz setup script:

```bash
vagrant ssh -c "sudo /var/www/html/_install/setup.sh"
```

The MySQL username is `root` and the password is `vagrant`.

#### That's it!

You can access the site at [http://localhost:8080](http://localhost:8080).

## Other Notes

If you run into any issues, feel free to submit them on GitHub. Also, if you want to contribute, pull requests are always welcome. (:
