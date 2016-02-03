$script = <<SCRIPT
  export DEBIAN_FRONTEND=noninteractive
  sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password vagrant'
  sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password vagrant'

  sudo apt-get update && sudo apt-get upgrade
  sudo apt-get install -y git-core lighttpd php5-cgi php5-mysqlnd php5-curl mysql-server curl eyeD3 atomicparsley imagemagick zip file

  sudo apt-get install -y php5-xdebug
  sudo cp /home/vagrant/conf/xdebug.ini /etc/php5/mods-available/xdebug.ini

  sudo lighty-enable-mod fastcgi fastcgi-php
  sudo service lighttpd force-reload

  sudo rm -f /var/www/html/index.lighttpd.html
SCRIPT

Vagrant.configure(2) do |config|
  config.vm.box = "debian/jessie64"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder "./app", "/var/www/html", :owner => "www-data", :group => "www-data"
  config.vm.synced_folder "./conf", "/home/vagrant/conf"

  config.vm.provider "virtualbox" do |vb|
    vb.name = "omgcatz"
    vb.cpus = 1
    vb.memory = "2048"
  end

  config.vm.provision "shell", inline: $script, keep_color: true, privileged: false
end
