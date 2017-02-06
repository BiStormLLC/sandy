# -*- mode: ruby -*-
# vi: set ft=ruby :

# 
# #ProjectSandy has been released as a Vagrant VirtualBox Ubuntu 16 environment 
#
# From Scratch:
# Prereq: Install Vagrant and VirtualBox on your Win/Lin/OSX machine
#
#

Vagrant.configure("2") do |config|
    config.vm.box = "bistorm/sandy"
    config.vm.box_url = "http://files.bistorm.us/sandy/metadata.json"

#
#   Not All Networks Work!
#
    #   Some networks will not work properly with bridged networking. Specifically, I've found that hotel networks, airport networks, and generally public-shared networks have configurations in place such that bridging does not work.
    #   You can tell if the bridged networking worked successfully by seeing if the virtual machine was able to get an IP address on the bridged adapter.
    #   https://friendsofvagrant.github.io/v1/docs/bridged_networking.html
    #

    config.vm.network :private_network, ip: "192.168.33.10", 
        auto_config: false
    config.vm.network :public_network, :public_network => "eth2", use_dhcp_assigned_default_route: true
    config.vm.network :public_network, :public_network => "wlan0", use_dhcp_assigned_default_route: true
    config.vm.network :public_network, :bridge => 'em1', use_dhcp_assigned_default_route: true

    config.vm.network "forwarded_port", guest: 9080, host:9084, auto_correct: false
    config.vm.network "forwarded_port", guest: 9081, host:8081, auto_correct: false
    config.vm.network "forwarded_port", guest: 9082, host:8082, auto_correct: false
    config.vm.network "forwarded_port", guest: 9083, host:8083, auto_correct: false
#
#   GUEST CUSTOMIZATIONS
#
    config.vm.hostname = "sandy"
    # Memory should not be over 1/4 of your host's memory.
    config.vm.provider :virtualbox do |v|
      v.customize ["modifyvm", :id, "--memory", 2048]
      v.customize ["modifyvm", :id, "--cpus", 2]
      v.customize ["modifyvm", :id, "--cpuexecutioncap", "80"]
    end

#    
#   PROBS ? 'Uh ... yeah?' : 'Try changing to the NFS file version and tweet @babelfeed if you still need help!'
#
    # <-- Paste one of these at the beginning of lines 45 - 47.  Then, remove them from lines 49-51.  Then, save this file.
    config.vm.synced_folder "./react-app", "/var/www/public/ux", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }
    config.vm.synced_folder "./bistorm", "/usr/local/bin/bistorm", owner: "vagrant", group: "root", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }
    config.vm.synced_folder "./SLUG", "/var/www/public/slug", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }

    #config.vm.synced_folder "./react-app", "/var/www/public/ux", owner: "vagrant", group: "www-data",  :mount_options => ["dmode=777", "fmode=666"] 
    #config.vm.synced_folder "./bistorm", "/usr/local/bin/bistorm", owner: "vagrant", group: "root",  :mount_options => ["dmode=777", "fmode=666"] 
    #config.vm.synced_folder "./SLUG", "/var/www/public/slug", owner: "vagrant", group: "www-data",  :mount_options => ["dmode=777", "fmode=666"] 
    
end
