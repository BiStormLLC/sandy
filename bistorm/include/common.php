<?php
namespace BiStorm;

error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once '/var/www/slug/slug.php';

##
#
#  TODO: Allow one Ip to serve vCum and one to serve IpTV
#
##
class Common {
    public $env;
    public $sandyIp = "localhost";
    public $sandyTvIp = "localhost";
    public $vCumulus; // UX interactions
    public $path; // URL path 

    public function __construct($env_name = 'release'){
        $this->sandyIp = self::getSandyIp();
        $this->sandyTvIp = self::getSandyTvIp();
        $this->path = self::getCurrentPathAsArray();
        if($env_name != 'release') {
            $this->env = $env_name;
        } else {
            $this->env = self::getVCumEnvName();
        }
    } 
    
    #  Ensures that the string is acceptable for URL use
    public static function cleanStringForUrl($text) {
        return preg_replace('[^a-z^A-Z^0-9^-]', "-", $text);
    }
    
    #
    public static function getCurrentPathAsArray() {
        return explode("/", $_SERVER['REQUEST_URI']);
    }
    
    # get the vCumulus Environment name set through storm shell init bash scripts
    public static function getVCumEnvName() {
        return vCumulus::getEnv();
    }
    
    # Sets the vCumulus Environment Object
    public function setVCumEnv($env_name = "release", $app_path) {
        $vCumulus = new vCumulus($env_name, $app_path);
        $this->vCumulus = $vCumulus;
        
        // TODO: Switch environments using storm, SLUG and $env_name 
        
        return $this;
    }
    
    // Get the network address stored in the vars directory
    private static function getSandyIp() {
        $env = self::getVCumEnvName();
        if( trim($env) == "stg-ext" ) {
            return "sandy1.bistorm.us";
        } else {
            $ip = shell_exec(". /vagrant/bistorm/vars/sandy_ip");
            $ip = substr($ip, strpos($ip, "@") + 1);
        }

        # Validate ip string before returning
        if(trim($ip) != "") {
            return trim($ip);
        } else {
            return false;
        } 
    }

    # Get the LiveStream Ip to serve
    public static function getSandyTvIp() {
        $env = self::getVCumEnvName();
        if( trim($env) == "stg-ext" ) {
            return "stream.bistorm.us";
        } else {
            $ip = shell_exec(". /vagrant/bistorm/vars/sandy_ip");
            $ip = substr($ip, strpos($ip, "@") + 1);
        }
        $ip = preg_replace( "/\r|\n/", "", $ip );

        # Validate ip string before returning
        if(trim($ip) != "") {
            return trim($ip);
        } else {
            return false;
        } 
    }
    
    # Set slug to the vCumulus Environment Object
    public static function addSlugToEnv($ux_env, $slug_required_apps) {
        $ux_env->slug = new \stdClass();
        $ux_env->server_name = self::getSandyIp();
        foreach( $slug_required_apps as $app_name => $app_body ) {
            $ux_env->slug->{$app_name} = new \BiStorm\SLUG\Slug('bistorm', $app_body, false);
        }
    }
    
    # Usecase: Redirect to an existing conversion channel if one is running in the 'c' app 
    public function getOpenChannels() {
        $urls = array();
        // SLUG integration
        if( ! $this->isSlugEnabled() ) {
            throw new \Exception('SLUG is not enabled for this environment.');
        }
        try{
            // Set active stream vars for hls and dash so we know what's currently active
            $this->vCumulus->slug->set_stream_vars->exec(false);
            ## Call the set_active_stream_vars through a new slug 'stream' action
            $active_hls_var = file_get_contents('/usr/local/bin/bistorm/stream/hls_streams');
            $active_hls_val = split("streams='", $active_hls_var)[1];
            $active_hls_val = split("'", $active_hls_var)[1];
            $active_dash_var = file_get_contents('/usr/local/bin/bistorm/stream/dash_streams');
            $active_dash_val = split("streams='", $active_dash_var)[1];
            $active_dash_val = split("'", $active_dash_var)[1];
            $hls_streams = explode(';', $active_hls_val);
            $dash_streams = explode(';', $active_dash_val);
            
            // Some cleanup
            foreach( $hls_streams as $stream ) {
                if($stream == "") {
                    unset($hls_streams[$stream]);
                }
            }
            foreach( $dash_streams as $stream ) {
                if($stream == "") {
                    unset($dash_streams[$stream]);
                }
            }     
        } catch (\Exception $ex) {
            return false;
        }

        return( array('hls' => $hls_streams, 'dash' => $dash_streams) );
    }
    
    public function getActivePublishers() {
        
    }
    
    public function getLiveStreamUrls() {
        $streams = $this->getOpenChannels();
        $urls = array();
        if( ! empty($streams['hls']) ) {
            foreach( $streams['hls'] as $stream ) {
                if( $stream == "" ) {
                    continue;
                }
                $stream_url = 'http://' . $this->getSandyTvIp() . '/iptv/' . $stream . '/index.m3u8';
                array_push($urls, $stream_url);
            }
        }
        if( ! empty($streams['dash']) ) {
            foreach( $streams['dash'] as $stream ) {
                if( $stream == "" ) {
                    continue;
                }
                $stream_url = 'http://' . $this->getSandyTvIp() . '/dash/' . $stream . '/index.mpd';
                array_push($urls, $stream_url);
            }
        }  
        return $urls;
    }
    
    ##
    #
    # Methods that require slug execution to set
    #
    ##
    
    # Is SLUG enabled for the attached environment?
    public function isSlugEnabled() {
        if ( is_object($this->vCumulus->slug) ) {
            return true;
        }
        return false;
    }

    # SLUG integration to determine if NGINX is currently serving HLS or DASH content
    public function isSandyStreaming() {
        $streams = $this->getLiveStreamUrls();
        // SLUG integration
        if( empty($streams) ) {
            return false;
        } 
        return true;
    }

    # SLUG integration to determine if FFMPEG processes are running
    public function isSandyConverting() {
        // SLUG integration
        if( ! $this->isSlugEnabled() ) {
            throw new Exception('SLUG is not enabled for this environment.');
        }
        // TODO
    } 
    
    public function getIptvStats() {
        if( ! $this->isSlugEnabled() || ! is_object($this->vCumulus->slug->log_view) ) {
            return false;
        }
        $this->vCumulus->slug->log_view->addActionArg( '1','stats' );
        $this->vCumulus->slug->log_view->exec(false);
        $response = $this->vCumulus->slug->log_view->slug_response;
        if( ! is_object($response->slug->msg->SLUG) ) {
            $server = $response->slug['msg']['SLUG']->server;
        }
        return $server;
    }
    
    public function getXndirProfiles($type='video') {
        $profiles = array();
        if( ! $this->isSlugEnabled() || ! is_object($this->vCumulus->slug->log_view) ) {
            return false;
        }
        $this->vCumulus->slug->profiles->addActionArg( '1', $type );
        $this->vCumulus->slug->profiles->exec(false);
        $response = $this->vCumulus->slug->profiles->slug_response;
        if( $response->slug['msg']['SLUG'] ) {
            $profiles = $response->slug['msg']['SLUG'];
        }
        
        return $profiles;
    }

    public function getClients($appid = 'c') {
        if( ! $this->isSlugEnabled() ) {
            return false;
        }
        $gathered_clients = array();

        $stats = (array)$this->getIptvStats();
        $apps = $stats['application'];
        foreach($apps as $app) {
           $app = (array)$app;
           if ( ! is_string($app['name']) || ! is_object($app['live']) || ! is_object($app['live']->stream->client) ) {
               continue;
           }
           if( $app['name'] == $appid || $app['name'] == 'hls' . $appid || $app['name'] == 'dash' . $appid ) {
               $stream = $app['live']->stream->client;
               foreach($stream as $client) {
                    if( (string)$client->address != '127.0.0.1' ) {
                        array_push( $gathered_clients, array('id'=>(string)$client->id, 'ip'=>(string)$client->address) );
                    }
                }
           }
        }
        return $gathered_clients;
    }
    
}