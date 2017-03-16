# -*- mode: ruby -*-
# vi: set ft=ruby :

# 
# Jen: February 27, 2017 
# #ProjectSandy has been released as a Vagrant VirtualBox Ubuntu 14.04 environment 
# Sandy Box: 0.4.4
# Sandy vCumulus 0.4.5
#
# From Scratch:
# Prereq: Install Vagrant and VirtualBox on your Win/Lin/OSX machine
#
# To install or run after halting, run 'vagrant up' in this directory
# To check if the Sandy Server instance is running, run 'vagrant status'
# To stop the Sandy Server instance, run 'vagrant halt'
# To start fresh, run 'vagrant halt' and then 'vagrant destroy'
# To update the Sandy Server environment, run 'vagrant box update all'
# To get the most recent version of this code repository, run "git pull origin master" in this directory
#

Vagrant.configure("2") do |config|

    # vCumulus latest release is handled by :9080, internal to the guest server
    #  run 'storm dev|stg-int|stg-ext|release' while ssh'd to change the environmental virtual host mappings
    config.vm.box = "bistorm/sandy"
    config.vm.box_url = "http://files.bistorm.us/sandy/metadata.json"
    config.vm.provision :shell, path: "./bistorm/storm", run: 'always'
    config.vm.provision "bootstrap", type: "shell" do |s|
        #s.inline = "/vagrant/bistorm/install_ffmpeg_latest" # Sandy Box 0.4.4 is provisioned with latest 
        #s.inline = "/vagrant/bistorm/install_nginx_with_rtmp_latest" # Sandy Box 0.4.4 is provisioned with latest
        s.inline = "/vagrant/bistorm/vcuum release dev >/dev/null"
        s.inline = "/vagrant/bistorm/vcuum release stg-int >/dev/null"
        s.inline = "/vagrant/bistorm/vcuum release stg-ext >/dev/null"
     end
    config.vm.network "forwarded_port", guest: 9081, host:8081, auto_correct: false #RTMP/HLS/DASH
    config.vm.network "forwarded_port", guest: 9082, host:8082, auto_correct: false #SLUG
    config.vm.network "forwarded_port", guest: 9083, host:8083, auto_correct: false #MongoDB
    config.vm.network "forwarded_port", guest: 5555, host:8555, auto_correct: false #MediaTomb
    config.vm.network "forwarded_port", guest: 9085, host:8085, auto_correct: false #vCumulus Prod app from localhost
    config.vm.network "forwarded_port", guest: 9086, host:8086, auto_correct: false #vCumulus Staging (internal)
    config.vm.network "forwarded_port", guest: 9087, host:8087, auto_correct: false #vCumulus Staging (external)
    config.vm.network "forwarded_port", guest: 9080, host:8084, auto_correct: false #vCumulus Default/release
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

    #   See this during setup? : Warning: "Remote connection disconnect. Retrying..."
    #   Or "Configuring nd enabling network interfaces"
    #       Keep waiting! It may look like 'vagrant up' didn't work, but it
    #       can take a while to provision the server for your network.

    #   SECURITY NOTICE
    #   Vagrant boxes are insecure by default and by design, featuring 
    #   public passwords, insecure keypairs for SSH access, and can potentially allow 
    #   root hacking access over SSH. With these known credentials, your box is easily 
    #   accessible by anyone on your network. Before configuring Vagrant to use 
    #   a public network, consider all potential security implications and review 
    #   the default box configuration to identify potential security risks.
    #
    #   IT IS POSSIBLE FOR SOMEONE TO EXECUTE CODE FROM A JPG
    #       Even if you aren't a big data company, someone can use a group of 
    #       computers to plant software and issue Denial of Service attacks USING YOUR BANDWIDTH.
    #       Imagine a cool world where everyone had a Sandy Box in their own homes.
    #       Without a properly configured firewall, someone could grab your 
    #       Sandy Server IP address, ssh in using 'vagrant:vagrant' credentials,
    #       Upload a script, and before you know it, you're an accomplice in a
    #       federal crime.  Don't be that person!  Set a new SSH Public Key in
    #       the ~/.ssh directory that YOU ALONE can connect to, if you decide
    #       you need your cloud media 'To Go'.        
    #   
    #   ANY device with an open SSH connection can be used not only to infiltrate
    #   the guest/host devices but also discover and hijack other known devices on the same network.

    config.vm.network "public_network", :public_network => "eth0"
    config.vm.network "public_network", :bridge => 'eth0', :use_dhcp_assigned_default_route => false
    #config.vm.network "private_network", ip: "192.168.33.10"

    # 2 GB memory or higher is recommended.
    # Typcally 1/2 of your CPU cores is a good tradeoff for background transcoding
    config.vm.provider :virtualbox do |v|
      v.customize ["modifyvm", :id, "--memory", 2048]
      v.customize ["modifyvm", :id, "--cpus", 4]
      v.customize ["modifyvm", :id, "--cpuexecutioncap", "97"]
    end
    
    #PROBS ? 'Uh ... yeah' : 'Try changing to the non-NFS file version and tweet @babelfeed if you still need help!'
    # <-- Paste one of these comment markers # at the beginning of these lines.  Then, remove them from lines 120-125.  Then, save this file and run 'vagrant up' again.
    
    ## ROOT folders
    ### !! WARNING !! SECURITY: Directories with root access can run scripts as sudo. TODO: Monitoring of write access to this directory, but making it easy to work with.
    config.vm.synced_folder "./bistorm", "/usr/local/bin/bistorm", owner: "vagrant", group: "root", :nfs => { :mount_options => ["dmode=775", "fmode=777"] }

    ## Apache accessible Web-App folders. * SLUG requires access to execute /bistorm scripts
    ### 555 = NO WRITE ACCESS, just read and execute scripts
    config.vm.synced_folder "./SLUG", "/var/www/slug", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=775", "fmode=555"] }
    ### 666 = NO EXECUTION at any level, just read and write
    config.vm.synced_folder "./vCumulus", "/var/www/public", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=754", "fmode=666"] } 

    #  VOD/Media Library *Option 1*: Store your media in the working directory of #ProjectSandy
    config.vm.synced_folder "./media", "/var/www/vod", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=775", "fmode=666"] }
    
    #  VOD/Media Library *Option 2*: Store your media in another location: this can be a NAS
    #  as long as you have created a virtual path to it on your host.
    #config.vm.synced_folder "A:/Media/Sandy", "/var/www/vod", owner: "vagrant", group: "www-data", :nfs => { :mount_options => ["dmode=775", "fmode=666"] }

    
    ###
    ###
    ###

    
    # OR >>> TRY THESE?
    #config.vm.synced_folder "./bistorm", "/usr/local/bin/bistorm", owner: "vagrant", group: "root", :mount_options => ["dmode=775", "fmode=664"] 
    #config.vm.synced_folder "./SLUG", "/var/www/slug", owner: "vagrant", group: "root", :mount_options => ["dmode=775", "fmode=555"] 
    #config.vm.synced_folder "./vCumulus", "/var/www/public", owner: "vagrant", group: "www-data", :mount_options => ["dmode=754", "fmode=666"] 
    #config.vm.synced_folder "./media", "/var/www/vod", owner: "vagrant", group: "www-data", :mount_options => ["dmode=777", "fmode=666"] 
    
end