# omgcatz

First and foremost, the goal of this project is to provide the user with an abundance of feline imagery—which it currently isn't very good at. However, it is capable of downloading and tagging music from the interwebz that is otherwise hard to acquire.

**Supported sites:**

* [8tracks.com](https://8tracks.com/)

**In development:**

* [songza.com](https://songza.com/)

## Developer Setup

**Prerequisites:**

* [VirtualBox](https://www.virtualbox.org/)
* [Vagrant](https://www.vagrantup.com/)

**Install necessary vagrant plugins:**

```bash
vagrant plugin install vagrant-vbguest
```

**Get the omgcatz stuff:**

```bash
git clone https://github.com/cadejscroggins/omgcatz && cd omgcatz
```

**Initialize the virtual machine:**

```bash
vagrant up
```

**Run the omgcatz setup script:**

```bash
vagrant ssh -c "sudo /var/www/html/_install/setup.sh"
```

The MySQL username is `root` and the password is `vagrant`.

**That's it!**

You can access the site at [http://localhost:8080](http://localhost:8080).

## Screenshots

![Kitteh](/screenshots/kitteh.png?raw=true "Kitteh")

![8tracks](/screenshots/eighttracks.png?raw=true "8tracks")
