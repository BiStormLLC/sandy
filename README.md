# BiStorm Labs Sandy Server 2017
# 
# Components
# global storm.sh : custom scripts for simplifying calls to ffmpeg for HLS
# slug : BiStorm node.js server-side applet for extending Sandy's services
#  SLUG = Service Levels Under Guest
# slug.silicondust : slug plugin for detecting HDHomerun Prime on network,
#  advertising channel and program listings, Web Config, etc
# Nginx, Apache, MongoDB, NodeJS, ReactJS (webpack and JSX with babel)
#
# RTMP and HLS  
# Nginx is used as a proxy and does not serve the WebApp. The WebApp is 
# hosted with Apache to make it more shareable.
# The box is setup to auto-forward guest requests:
#  1. port 80 to port 9080 (Cumulus UX)
#  2. port 81 to port 9081 (HLS streaming)
#  3. port 82 to port 9082 (Slug Extension Manager UI)
#  4. port 83 to port 9083 (MongoDB)
#  using local.rc.  Apache manages Sandy WebApp requests on port 8080.
#  RTMP requests to the guest on port 81 are forwarded to port 8081 using 
#  this Vagrantfile.  Port 9081 on the guest points to nginx HLS directory.

##
#
# Tutorials and resources we used
#
# HLS
# https://docs.peer5.com/guides/setting-up-hls-live-streaming-server-using-nginx/
#
##