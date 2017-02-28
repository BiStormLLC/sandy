# BiStorm Labs Sandy Server 2017
# ******************************
 The BiStorm #ProjectSandy (Instagram and Twitter) Server is a media format translation and 
  cloud-based virtual communications server.  It is licensed under GNU and is freely 
  distributed and available via BiStorm's GitHub account: http://github.com/BiStormLLC
 The intention of this application is for educational purposes, but it is not limited to this use.
 If you intend to use Sandy's television program listing 
 (or transcoding services, depending on the codec: see 
   https://www.raspberrypi.org/forums/viewtopic.php?f=63&t=35818&start=150) 
 You MUST agree to ONLY install this virtual server in an environment that is for personal use
   if the license of other software library distributors requires it.  
 However for every other purpose, this software package is intended to be used by the general public.
 There are very great industry benefits to adopting this as a homebrew platform:
   > Online video platforms require less bandwidth if they receive pre-compressed files
   > BiStorm prides itself, if anything, on maintainability and provisioning of its software
   > Churches and event-based mediums can more easily broadcast to small audiences, and in future releases, 
        have remote audience members contribute their own bandwidth for better quality services for everyone.

## LICENSE: Files in the bistorm and SLUG directories are owned, written and developed by BiStorm, LLC in Tacoma, WA
   All other software in this repository is subject to its own licenses, terms and agreements

## AUTHORS/CONTRIBUTORS/SUPPORTERS
### @EveEqualsMc2

## SUPPORT
### @babelfeed

## TRACK OUR PROGRESS ON INSTAGRAM
### https://www.instagram.com/jonorjenmcquade/

## PROJECT FILES: http://github.com/BiStormLLC

## CURRENT VER: vCumulus UX 0.4.5 SLUG 0.1.1 SandyBox 0.4.4

>No, you may not see our timetables

### TODO: FULL PACKAGE OF SILICONDUST EVENT HANDLERS AND EPG SETUP THROUGH SLUG

# INSTALLATION
 
## PREREQUISITES
### 1.  Install VirtualBox: https://www.virtualbox.org/wiki/Downloads
### 2.  Install Vagrant: https://www.vagrantup.com/docs/installation

## VIRTUAL HARDWARE (to accommodate future Banana Pi flashing): https://en.wikipedia.org/wiki/Banana_Pi
### These can be changed in the Vagrantfile, located in this directory
 1.  Memory is set at 2GB 
 2.  CPUs is set to 4

## USING SANDY AS A PERSONAL MEDIA SERVER
Sandy has a key role in BiStorm's virtual dashboarding initiatives, allowing some 
streaming resources to be offloaded to a lighter machine in our network, so that
a VR demonstration machine can be better utilized without additional running background services.

With that, the Sandy server itself is a great addition to anyone's network-
accessible media management and archiving strategy, generating an on-demand media NAS 
(Network Accessible Storage), provided that you adhere to
the licensing constraints of the open-source packages in this environment.

## A Personal DVR and Small Business Media Server, on-demand, with restore

### tvheadend: Along with mediatomb (UPnP) and minidlna (DLNA), tvheadend has 
also been installed in this project, to allow you to configure your own adhoc DVR.  
To setup and configure this feature, you will need to know the IP address of Sandy.  
Once you have this, visit http://[SANDY_IP]:9981 and enter "vagrant" as the username and password.

## EASY TO STOP, EASY TO RESTORE
 Anytime you would like to stop server-ing, simply run "vagrant halt" in the
 location where you cloned the project, and the Sandy Server instance will shut down.  To 
 destroy the server entirely, run "vagrant destroy".  To rebuild, run "vagrant up"
 again, as you did to install the server initially.  
 > Be careful of IP conflicts if you do several installations in a small amount of time! The Sandy Server is assigned an IP address dynamically from your router. #YourRouterIsSmarterThanUs


## STREAMING A BROADCAST FROM YOUR DESKTOP?
 The Sandy Server will accept an RTMP MP4 stream in its native format, without
 transcoding or transmuxing.  Open Broadcaster Software (https://obsproject.com/)
 can be setup in its settings to stream to an RTMP server.  The format for streaming
 to the Sandy Server is: rtmp://[SANDY_IP]/d and the stream "key" or "name" will be used as
 the URL format to access the stream in a browser like this:
 http://[SANDY_IP]/d/[STREAM_NAME]
 > The HLS stream file can be accessed like this: http://[SANDY_IP]/d/[STREAM_NAME]/index.m3u8
 > The .m3u8 file can be targeted in IPTV software such as Google TV "Channels" app.  This can be accomplished using Cumulus (no relation to BiStorm vCumulus UX): https://play.google.com/store/apps/details?id=com.felkertech.n.cumulustv&hl=en
 > The RTMP server is also capable of streaming the new MPEG streaming standard, "DASH".  This is not implemented in the transcoding process for live television because DASH generates multiple files for different bandwidth constraints, and this process is too CPU-intensive for our use-cases.


## WATCHING A SHOW OF SOME KIND? 
 The Sandy Server has CPU-based transcoding of your HDHomeRun channels, allowing you to
 watch SD and HD television signals, converted to 540p, without an additional broadcasting input such as Windows app.
 To cast any particular SiliconDust HDHomeRun channel and watch in any browser:
 After the server boots, visit http://[SANDY IP]/c/[CHANNEL NUMBER]
### Note: Because this is a virtual machine, there is no hardware encoding for video and we're willing to wager that you won't see a GPU in the next Pi device.

> 1. In a shell/terminal window, navigate to the directory of this folder.
> 2.  Type in, "vagrant up" and press Enter.
> 3.  This is a great time to get to know your friends better.  You'll be down-
       loading for a while.  If you're returned back to your cursor, you're 
       good to go. Total file size of the #ProjectSandy environment is about 1 GB. 
> 4.  Type in "vagrant status".  
> 5.  Is the Virtual Machine running?
       a. Yes -> Go on to 6.
       b. Tweet @babelfeed a screenshot and let's work through it together
> 6. The HDHomeRunPrime can now be navigated through Sandy's first UX iteration
       On a device in your same subnet (ethernet or wifi), visit:
           http://[SANDY IP]/c/[CHANNEL NUMBER]
> 7. You should see a video buffering animation as it establishes an open 
       connection to the HDHomeRunPrime, then should begin streaming MP4,
       with about a 3-4 second delay, depending on device range and latency.
 Special Note: Do not be that guy that makes us all look bad by opening your 
       Sandy Server instance to a public DNS in order to share your subscription TV channels. 
       Make good choices with the media that others share with you, yeah?
       #LetsNotGetAllLegal #DoNoEvil #StillBroke #DoNotKickstartOurLawyers

## WANT TO STREAM YOUR DESKTOP WITHOUT A CLOUD MEDIA SERVICE LIKE TWITCH? 
 Sandy comes installed with an RTMP server address, just like live video apps
   give you when you register with them.  Your RTMP address format is:
   rtmp://[SANDY_IP]:1981/d/[STREAM NAME]
   From there, Sandy will serve your stream to this address, which you can open
     in any app that can view MP4 streaming video (such as IPTV apps or Mobile Safari):
   http://[SANDY_IP]:9081/d/[STREAM_NAME]/index.m3u8
   And you can view it inside of vCumulux UX here:
   http://[SANDY_IP]/d/[STREAM_NAME]
   *Authentication has not been setup for this environment.
   **Please tweak cautiously.

## vCumulus WILL NOT CLOUD YOUR JUDGMENT: "Is the heat in our cookies?"
 Just a note on analytics: BiStorm does not track your use of any streaming
 service.  We have responsible jobs here, and none of them are to monitor your activity.
 However, we do like to know when someone is using our products.
 vCumulus, Sandy's browser UX, uses Google Analytics to track page-level analytics.
 Guess what?  So do about 90% of Web Sites, which is why they have Privacy Policies.
 You can easily stop this tracking with little effort by removing the Google Analytics block in primary index file of the /vagrant/vCumulus/$env/react-app folder.

## vCumulus Entry Router Examples: Go here to watch your streams in a browser
 http://[SANDY_IP]/c/13 (opens channel 13 using Sandy's mpegts to mp4 conversion services)
 http://[SANDY_IP]/d/show (opens stream "show" initiated through an RTMP request)

## Components
### global storm.sh : custom scripts for simplifying on-boot processes without editing via ssh
### slug : BiStorm PHP (later, NodeJS) server-side applet for extending Sandy's guest-level communications down to /vagrant/bistorm shell and bash scripts
  SLUG = Service Levels Under Guest (Currently Apache/PHP, later NodeJS)
### TODO: slug.silicondust : slug plugin for detecting HDHomerun Prime on network,
  advertising channel and program listings (if server is for personal use), Web Config, etc
### Nginx, Apache, MongoDB, NodeJS, ReactJS (webpack and JSX with babel ES2015)

## RTMP and HLS and MP4, OH MY!  
 Nginx is used only for RTMP and HLS streaming, and does not serve the WebApp. The WebApp is 
 hosted with Apache to make it more shareable and friendly for developers of all ages.
 The box is setup to auto-forward inbound requests as shortcuts, using the Ubuntu /etc/local.rc file:
 >  1. port 80 to port 9080 (Sandy vCumulus UX)
 >  2. port 81 to port 9081 (HLS streaming)
 >  3. port 82 to port 9082 (Slug Extension Manager)
 >  4. port 83 to port 9083 (MongoDB)
 Apache listens (manages) Sandy Web App requests on port 9080. 
 Port forwarding from your host at port http://localhost:9084 also points to vCumulus UX
 HTTP requests to the host on port 8080 are forwarded to port 80 using 
  default Vagrant behavior.  /etc/local.rc forwards port 80 to port 9080 (again, vCumulux UX).
 Ports 1981 and 1935 on the Sandy server point to the RTMP server
 Many public wifi hotspots do not allow port 1935 communications, but may not block others by default, hence an additional opening
 > rtmp://[SANDY_IP]:1981/d/HLS_NAME
 > rtmp://[SANDY_IP]/d/HLS_NAME
 Port 9082 on the Sandy Server points to Sandy SLUG guest processes, currently running using Apache and PHP: 
 > http://[SANDY_IP]:9082/action/stop - quit processing any ffmpeg transoding jobs
 > http://[SANDY_IP]:9082/c/XYZ - initiate an HLS broadcast from the HDHomeRun 
   channel entered, if a HDHomeRun device is found.
   example: http://[SANDY_IP]:9082/c/13

## 3rd Party Applications, Packages and Plugins to help you discover and serve your media
>  1. tvheadend: https://github.com/tvheadend/tvheadend (http://[SANDY_IP]:9981)
>  2. mediatomb: http://mediatomb.cc/pages/download (http://[SANDY_IP]:5555)
>  3. minidlna: https://github.com/azatoth/minidlna (http://[SANDY_IP]:9500)
>  4. jwplayer: https://developer.jwplayer.com/ (part of #ProjectSandy vCumulus UX)
>  5. ffmpeg: https://ffmpeg.org/ (transcoding processes for media conversion)
>  6. Apache, Nginx, NodeJS/NPM, MongoDB, Webpack, ReactDOM for Web hosting

## ROUTING OF BROWSER REQUESTS TO GUEST SERVICES
Please note that in these examples, localhost/127.0.0.1/0.0.0.0 is more likely 
 interpreted inside of the server, but for the sake of understanding the 
 relationship between the host (your machine) and the guest, Sandy, we have 
 diagrammed these using the physical IP address that VirtualBox established.

###   #Q : WHAT IS SANDY'S localhost IP ADDRESS?
###   #A : Sandy establishes a virtual private network with your host machine.
        This virtual IP address is not accessible to the rest of your network.
        This is for 'hardening' your staging projects using this server.
        You can disable the network hardware on eth1 and above and still be
        able to access your Sandy server from http://192.168.33.10

###   #Q : WHAT IS SANDY'S LAN IP ADDRESS?
###   #A : The Sandy Server will output this when you rung 'vagrant up'
        Your router determines this address and you may have a few, if both wifi and 
        ethernet hardware are available.  There are many ways to find an IP address, but
        the easiest way is to connect to the server by running 'vagrant ssh', 
        then run 'ifconfig -a'.  Sandy will show you her virtual networking hardware,
        which is actually your hosts's hardware. Typically, your 'eth0' adapter
        will display an inet addr for your router and 'eth1' or 'wlan0' items
        will give you a "192.168.*.*" address.  This is your Sandy Server IP.

###   --> TO IP OR NOT TO IP?
   We at BiStorm believe that posting IP addresses in any document is just about
     an act of treason to the InfoSec community. It's also difficult to debug.
   To make the development environment more friendly to those without a DNS server,
   the IP address of the server instance is inserted dynamically into the pages it serves.
                            In other words ...
   !! WE DID NOT HARD CODE YOUR DYNAMICALLY CREATED IP ADDRESS INTO OUR SOFTWARE !!
                                ...
   To make development easier, we HIGHLY recommend pointing your Sandy Server IP address to a domain using
     your networking "hosts" file to resolve the name internal to your own machine.  Here are some tips:
     http://www.howtogeek.com/howto/27350/beginner-geek-how-to-edit-your-hosts-file/

##   --> EXAMPLES of round-trip routing: 
### From your host machine
> 1.  http://localhost:9084/c/13 -> Vagrant port forwarding to http://[SANDY_IP]:9080/c/13, Apache listener responds with vCumulus UX (/var/www/public/$env/react-app). 
> 2.  http://localhost:8083 -> Vagrant port forwarding to http://[SANDY_IP]:9083, Nginx proxy forwards to MongoDB listener responds (#TODO)

### From sandy.bistorm.local (with modified hosts file)
> 1.  http://sandy.bistorm.local -> Host DNS resolution to [SANDY_IP]:80, Nginx port 80 proxy forwards to Apache at http://[SANDY_IP]:9080, Apache listener responds with vCumulux UX
> 2.  http://sandy.bistorm.local:8081/hlsc/13/index.m3u8 -> Host DNS resolution to http://[SANDY_IP]:9081/hlsc/13/index.m3u8, Nginx HTTP listener responds

## Tutorials and resources we used
### HLS
 https://docs.peer5.com/guides/setting-up-hls-live-streaming-server-using-nginx/
 
### EPG (#TODO)
 https://github.com/SchedulesDirect/JSON-Service/wiki/API-20141201

### Server Provisioning
 https://scotch.io/tutorials/how-to-create-a-vagrant-base-box-from-an-existing-one
 https://www.vagrantup.com/docs/networking/public_network.html

### HDHomeRun Development
 https://www.silicondust.com/hdhomerun/hdhomerun_development.pdf
 
### FFmpeg Streaming Settings Guide
 https://trac.ffmpeg.org/wiki/EncodingForStreamingSites

### Writing scripts for Sandy in shell
 https://techarena51.com/index.php/inotify-tools-example/
 http://www.tldp.org/HOWTO/Bash-Prog-Intro-HOWTO-6.html
 
### Troubleshooting
 http://stackoverflow.com/questions/22717428/vagrant-error-failed-to-mount-folders-in-linux-guest
 https://dmsimard.com/2014/05/02/no-network-on-ubuntu-14-04-cloud-image-with-cloud-init/

## Change history

# WHAT TO EXPECT NEXT
-  MongoDB integration for configuration of simulcasting services (Twitch, YouTube, etc.)
-  Metadata handlers for converted media
-  More work for clustering Sandy servers in a network and taking an instance to production
-  HLS Encryption: http://docs.aws.amazon.com/elastictranscoder/latest/developerguide/content-protection.html

# WHAT TO EXPECT IN THE FUTURE
-  Video text-to-URL conversion inside vCumulus UX
-  Disruptive marking ploys to ask for your hard-earned cash


### 0.4.5
    -Split basebox, vCumulus, SLUG into different versions. No version requirements for X^nDir
    -Vagrant Virtualbox basebox will be maintained at 0.4.4 without a new version. Future updates will be done through provision scripts unless there are major releases.
    -vCumulus is broken out as the primary version for the #ProjectSandy environment (an aliased version).
    -BiStorm SLUG is currently at 0.1.1.  Will be refactoring for namespaces, to prep for other #IOT communication projects
    -Broke vCumulus out into environments.  By default, server runs through /vagrant/vCumulus/release.  To host a different environment, ssh into the guest and run "storm [dev|stg-int|stg-ext]".
    -To elevate your code to a different environment, ssh into the guest and run "vcum [From Environment] [To Environment].  This is mirrored-elevation compatible, meaning you can copy "release" into "dev" or "dev" into "release"
    -Birth of X^nDir, our media format standardizer. This includes profile conversion updates to allow drag-and-drop conversions from the vagrant/convert folders.
    -Profiled transcoding of HDHomeRun channels, to allow you to tune as you'd like.
    -Nginx config updates to reverse-proxy incoming port 80 requests to Apache for vCumulus
    -Much better HDHomeRun conversion speed and quality
    -Initial DVR clip efforts, to implement timeshifting in a future release.
    -Refactoring, refactoring, refactoring.  Check the github master branch timeline for diffs.
### 0.4.0
    -Configured nginx for /z RTMP adaptive streaming
    -Configured nginx and nginx-rtmp modules for DASH HTTP streaming support
    -Automatic post-processing of published RTMP/FLV to MP4 using profile folders
    -Cron-based detection of new media files for in-place conversion in profile folders
    -Manual build of ffmpeg for x265 support in Sandy Server base box
    -Installation of Ruby on Rails in Sandy Server base box
    -Thumbnail generation of published video files
### 0.3.5
    -Configured nginx for /c and /d RTMP directories
    -Modified HLS output directories to /var/www/hlsc and /var/www/hlsd
    -Verified RTMP listener can respond to request through Open Broadcast Software
    -tvheadend installed: configure through http://[SANDY_IP]:9981 user: vagrant password: vagrant
### 0.3.4
    -Some additional server provisioning to resolve networking hardware hangs
    -Better mobile support in vCumulus UX
    -Better support of killing ffmpeg streams through bistorm/ffmpeg-kill
### 0.3.3
    -First public release of bistorm/sandy server on HashiCorp Atlas cloud
    -https://atlas.hashicorp.com/vagrant
### 0.3.0
    -All the things (first official code release)
