# BiStorm Labs Sandy Server 2017
# Custom Packages for Provisioned Sandy Servers
# ******************************
When at all possible, BiStorm only installs from publicly accessible repositories
    and does not use custom builds of libraries required for its software.
However, in order to maintain a development edge, the pre-provisioned server
    environment does make use of some packages which require manual maintenance 
    and upgrades, for example, if there are new security patches available.
Please tweet @babelfeed if you wish to report a need for BiStorm to recompile
    its dependencies with these libraries.

## NGINX
Nginx is intended to be used solely (not primarily) as an RTMP host, allowing for
    in-process and post-processing handlers for HLS and DASH HTTP streaming.
Because of this requirement, certain custom modules are required to be installed
    along with Nginx in order to accomplish this goal:
### Nginx Conf can be configured using /vagrant/bistorm/conf/nginx.conf
        
> nginx-rtmp-module https://github.com/arut/nginx-rtmp-module
> nginx-stub-status-module https://nginx.org/en/docs/http/ngx_http_stub_status_module.html

## FFMpeg
FFMpeg has been compiled from source with all default options plus h265 support.
> https://trac.ffmpeg.org/wiki/CompilationGuide/Ubuntu
> Assembled with Yasm (installed from source)
> Compiled with libx264, libx265, libfdk-aac, libmp3lame, libopus, libvpx

