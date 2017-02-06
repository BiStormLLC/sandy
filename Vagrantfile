# -*- mode: ruby -*-
# vi: set ft=ruby :

# 
# Jen: February 3, 2017 
# #ProjectSandy has been released as a Vagrant VirtualBox Ubuntu 16 environment 
# for version 0.3.0.
#
# From Scratch:
# Prereq: Install Vagrant and VirtualBox on your Win/Lin/OSX machine
#
#

Vagrant.configure("2") do |config|
    config.vm.box = "bistorm/sandy"
    config.vm.box_url = "http://files.bistorm.us/sandy/metadata.json"
    config.vm.network "forwarded_port", guest: 9080, host:9084, auto_correct: false
    config.vm.network "forwarded_port", guest: 9081, host:8081, auto_correct: false
    config.vm.network "forwarded_port", guest: 9082, host:8082, auto_correct: false
    config.vm.network "forwarded_port", guest: 9083, host:8083, auto_correct: false
    config.vm.hostname = "sandy"
    
    #   Warning!  Not All Networks Are Created Equal
    #   Some networks will not work properly with bridged networking. 
    #   Specifically, I've found that hotel networks, airport networks, and 
    #   generally public-shared networks have configurations in place such that 
    #   bridging does not work.
    #   You can tell if the bridged networking worked successfully by seeing if 
    #   the virtual machine was able to get an IP address on the bridged adapter.
    #   https://friendsofvagrant.github.io/v1/docs/bridged_networking.html
    #

    #  See this during setup? : Warning: "Remote connection disconnect. Retrying..."
    #  Or "Configuring nd enabling network interfaces"
    #       Keep waiting! It may look like 'vagrant up' didn't work, but it
    #       can take a while to provision the server for your network.

    #   SECURITY NOTICE
    #   Vagrant boxes are insecure by default and by design, featuring 
    #   public passwords, insecure keypairs for SSH access, and potentially allow 
    #   root access over SSH. With these known credentials, your box is easily 
    #   accessible by anyone on your network. Before configuring Vagrant to use 
    #   a public network, consider all potential security implications and review 
    #   the default box configuration to identify potential security risks.

    config.vm.network "public_network", :public_network => "eth0"
    config.vm.network "public_network", :bridge => 'eth0', :use_dhcp_assigned_default_route => false
    config.vm.network "private_network", ip: "192.168.33.10"

    # 2 GB memory.
    config.vm.provider :virtualbox do |v|
      v.customize ["modifyvm", :id, "--memory", 2048]
      v.customize ["modifyvm", :id, "--cpus", 2]
      v.customize ["modifyvm", :id, "--cpuexecutioncap", "95"]
    end
    
    #PROBS ? 'Uh ... yeah' : 'Try changing to the non-NFS file version and tweet @babelfeed if you still need help!'
    # <-- Paste one of these at the beginning of lines 19 - 21.  Then, remove them from lines 23-25.  Then, save this file.
    config.vm.synced_folder "./react-app", "/var/www/public/ux", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }
    config.vm.synced_folder "./bistorm", "/usr/local/bin/bistorm", owner: "vagrant", group: "root", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }
    config.vm.synced_folder "./SLUG", "/var/www/public/slug", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=777", "fmode=666"] }

    #config.vm.synced_folder "./react-app", "/var/www/public/ux", owner: "vagrant", group: "www-data",  :mount_options => ["dmode=777", "fmode=666"] 
    #config.vm.synced_folder "./bistorm", "/usr/local/bin/bistorm", owner: "vagrant", group: "root",  :mount_options => ["dmode=777", "fmode=666"] 
    #config.vm.synced_folder "./SLUG", "/var/www/public/slug", owner: "vagrant", group: "www-data",  :mount_options => ["dmode=777", "fmode=666"] 
    
end