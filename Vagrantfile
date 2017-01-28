# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "http://files.bistorm.us/sandy.box"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.network "forwarded_port", guest: 9081, host:81, auto_correct: false
    config.vm.network "forwarded_port", guest: 9082, host:82, auto_correct: false
    config.vm.network "forwarded_port", guest: 9083, host:83, auto_correct: false
    config.vm.provider :virtualbox do |v|
      v.customize ["modifyvm", :id, "--memory", 1024]
    end
    config.vm.hostname = "scotchbox"
    config.vm.synced_folder "./react-app", "/var/www/public", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }
    
    # Optional NFS. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

end
