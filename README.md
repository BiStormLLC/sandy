#
# BiStorm Labs Sandy Server 2017
# ******************************
# The BiStorm #ProjectSandy (Instagram and Twitter) Server is a media format translation and 
#  cloud-based virtual communications server.  It is licensed under GNU and is freely 
#  distributed and available via BiStorm's GitHub account: http://github.com/BiStormLLC
# The intention of this application is for educational purposes, but it is not limited to this use.
# If you intend to use Sandy's television program listing services, you must agree to ONLY
#  install this virtual server in an environment that is for personal use.  
# However for every other purpose, this software package is intended to be used by the general public.
#

# LICENSE: Files in the bistomr and SLUG directories are owned, written and developed by BiStorm, LLC
# All other software in this repository is subject to its own licenses, terms and agreements

# VER 0.3.0

# #ProjectSandy Server with vCumulus UX 

# *No, you may not see our timetables

# TODO: FULL PACKAGE OF SILICON DUST EVENT HANDLERS AND EPG SETUP

# 
# PREREQUISITES
# 1.  Install VirtualBox: https://www.virtualbox.org/wiki/Downloads
# 2.  Install Vagrant: https://www.vagrantup.com/docs/installation
#

#
# VIRTUAL HARDWARE: these can be changed in Vagrantfile, located in this directory
# 1.  Memory is set at 1GB
# 2.  CPUs is set to 2.
#

# WATCHING A GAME OF SOME KIND? 
# To cast any particular SiliconDust HDHomeRun channel and watch in any browser:
# After the server boots, visit http://[SANDY IP]/c/[CHANNEL NUMBER]
#
# 1. In a shell/terminal window, navigate to the directory of this folder.
# 2.  Type in, "vagrant up" and press Enter.
# 3.  This is a great time to get to know your friends better.  You'll be down-
#       loading for a while.  If you're returned back to your cursor, you're 
#       good to go. Total file size of the #ProjectSandy environment is about 1 GB. 
# 4.  Type in "vagrant status".  
# 5.  Is the Virtual Machine running?
#       a. Yes -> Go on to 6.
#       b. Tweet @babelfeed a screenshot and let's work through it together
# 6. The HDHomeRunPrime can now be navigated through Sandy's first UX iteration
#       On a device in your same subnet (ethernet or wifi), visit:
#           http://[SANDY IP]/c/[CHANNEL NUMBER]
# 7. You should see a video buffering animation as it establishes an open 
#       connection to the HDHomeRunPrime, then should begin streaming MP4,
#       with about a 3-4 second delay, depending on device range and latency.
# Special Note: Do not be that guy that makes us all look bad, by opening your 
#       Sandy Server to a public DNS to share your subscription TV channels. 
#       Make good choices with the media that others share with you, yeah?
#       #LetsNotGetAllLegal #DoNoEvil #StillBroke
#                                            --You pay for apps? Oh that's nice!  
#                                                   http://PayPal.Me/BiStorm/P2P
#                                        
#                                         Your friendly cloud-based angels of IT.
#                                                                   BiStorm, LLC
#                                                               Tacoma, WA 98404
#

# COMING SOON : 
# WANT TO STREAM YOUR DESKTOP WITHOUT A CLOUD MEDIA SERVICE LIKE TWITCH? 
# Sandy comes installed with an RTMP server address, just like live video apps
#   give you when you register with them.  Your RTMP address format is:
#   rtmp://192.168.1.123:1981/d/[STREAM NAME]
#   *Authentication has not been setup for this environment.
#   **Please tweak cautiously.
#

# Browser Entry Routers
# *Sandy IP address is assumed.  It might be different in your network.
# http://192.168.1.123:9082/action/stop - quit processing any jobs
# http://192.168.1.123:9081/channel/vXYZ.0 - initiate an HLS broadcast 
#   from the channel entered
#   example: http://192.168.1.123:9081/channel/513

# 
# Components
# global storm.sh : custom scripts for simplifying calls to ffmpeg for HLS
# slug : BiStorm node.js server-side applet for extending Sandy's services
#  SLUG = Service Levels Under Guest (Currently Apache/PHP, later NodeJS)
# slug.silicondust : slug plugin for detecting HDHomerun Prime on network,
#  advertising channel and program listings (if server is for personal use), Web Config, etc
# Nginx, Apache, MongoDB, NodeJS, ReactJS (webpack and JSX with babel)
#
# RTMP and HLS and MP4, OH MY!  
# Nginx is used as a proxy and does not serve the WebApp. The WebApp is 
# hosted with Apache to make it more shareable.
# The box is setup to auto-forward inbound requests as shortcuts, using the Ubuntu local.rc file:
#  1. port 80 to port 9080 (Sandy vCumulus UX)
#  2. port 81 to port 9081 (HLS streaming)
#  3. port 82 to port 9082 (Slug Extension Manager)
#  4. port 83 to port 9083 (MongoDB)
#  USING --> local.rc.  Apache currently manages Sandy Web App requests on port 9080.
#  HTTP requests to the guest on port 80 are forwarded to port 9081 using 
#       the Vagrantfile in this directory.  Port 9081 maintains the apps for browser requests.
#  Port 1981 on the guest points to the RTMP server (rtmp://192.168.33.10:1981/c/HLS_NAME)
#

##
# ROUTING OF BROWSER REQUESTS TO GUEST SERVICES
# Please note that in these examples, localhost/127.0.0.1/0.0.0.0 is usually what is 
#   interpreted inside of the server, but for the sake of understanding the 
#   relationship between the host (your machine) and the guest, Sandy, we have 
#   diagrammed these using the physical IP address that VirtualBox established.
#
#   #Q : WHAT IS SANDY'S localhost IP ADDRESS?
#   #A : Sandy establishes a virtual private network with your host machine.
#        This virtual IP address is not accessible to the rest of your network.
#        This is for 'hardening' your staging projects using this server.
#        You can disable the network hardware on eth1 and above and still be
#        able to access your Sandy server from http://192.168.33.10
#
#   #Q : WHAT IS SANDY'S LAN IP ADDRESS?
#   #A : Your router determines this.  There are many ways to find it, but
#        the easiest way is to run 'vagrant ssh' to connect into your Sandy server,
#        then run 'ifconfig -a'.  Sandy will show you her virtual networking hardware,
#        which is actually your hosts's hardware. Typically, your 'eth0' adapter
#        will display an inet addr for your router and 'eth1' or 'wlan0' items
#        will give you a "192.168.*.*" address.  This is your Sandy Server IP.
#
#   --> TO IP OR NOT TO IP?
#   We at BiStorm believe that posting IP addresses in any document is just about
#     an act of treason to the InfoSec community. It's also difficult to debug.
#   We HIGHLY recommend pointing your Sandy Server IP address to a domain using
#     your networking "hosts" file.  Here are some tips:
#     http://www.howtogeek.com/howto/27350/beginner-geek-how-to-edit-your-hosts-file/
#
#   --> EXAMPLES of round-trip routing: 
# From your host machine
# 1.  http://localhost:9084/c/13 -> Vagrant port forwarding to http://192.168.1.123:9080/c/13, 
        Apache listener responds with vCumulus UX (/var/www/public/ux).
# 2.  http://localhost:8083 -> Vagrant port forwarding to http://192.168.1.123:9083, MongoDB 
#       listener responds (#TODO)
#
# From sandy.bistorm.local (with modified hosts file)
# 1.  http://sandy.bistorm.local -> local.rc port forwarding to http://192.168.10.33:9080,
#       Apache listener responds with vCumulux UX
# 2.  http://sandy.bistorm.local:81/13.m3u8 -> local.rc port forwarding to http://192.168.10.33:9081/13.m3u8,
#       Nginx listener responds
##


##
#
# Tutorials and resources we used
#
# HLS
# https://docs.peer5.com/guides/setting-up-hls-live-streaming-server-using-nginx/
# 
# EPG (#TODO)
# https://github.com/SchedulesDirect/JSON-Service/wiki/API-20141201
#
# Server Provisioning
# https://scotch.io/tutorials/how-to-create-a-vagrant-base-box-from-an-existing-one
# https://www.vagrantup.com/docs/networking/public_network.html
#
# HDHomeRun Development
# https://www.silicondust.com/hdhomerun/hdhomerun_development.pdf
# 
# FFmpeg Streaming Settings Guide
# https://trac.ffmpeg.org/wiki/EncodingForStreamingSites
#
# Writing scripts for Sandy in shell
# https://techarena51.com/index.php/inotify-tools-example/
# http://www.tldp.org/HOWTO/Bash-Prog-Intro-HOWTO-6.html
# 
# Troubleshooting
# http://stackoverflow.com/questions/22717428/vagrant-error-failed-to-mount-folders-in-linux-guest
# https://dmsimard.com/2014/05/02/no-network-on-ubuntu-14-04-cloud-image-with-cloud-init/
#
##

##
#
# Change history
#
##

0.3.0
    -All the things (first official code release)
0.3.3
    -First public release of bistorm/sandy server on HashiCorp Atlas cloud
    -https://atlas.hashicorp.com/vagrant
0.3.4
    -Some additional server provisioning to resolve networking hardware hangs
    -Better mobile support in vCumulus UX
    -Better support of killing ffmpeg streams through bistorm/ffmpeg-kill
