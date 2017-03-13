<?php
namespace BiStorm;

error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**
 * Self-contained methods and properties for accelerating development in the vCumulus UX environment
 */
class vCumulus {
    public function __construct($env_name = 'release', $app_path) {  
        $this->app_path = $app_path;
        $this->channel = self::getChannel();
        $this->appId = self::getAppId();
        $this->streamType = self::getStreamType();
        $this->channelActivationUrl = self::getChannelActivationUrl();
        $this->iptvRoot = self::getIptvRoot();
        $this->iptvUrl = self::getIptvUrl();
        $this->dashUrl = self::getDashUrl();
        $this->rtmpUrl = self::getRtmpUrl();
        $this->hdhrIp = self::getHdhrIp();
        $this->env = self::getEnv();
        $this->channel_lineup = self::setChannelLineup(); //$this->channel_lineup
    }
    
    // Get the vCumulus environment
    public static function getEnv() {
        $output = shell_exec(". /vagrant/bistorm/vars/vcum_env");
        $env = substr($output, strpos($output, "@") + 1);
        if(trim($env) != "") {
            return trim($env);
        } else {
            return false;
        } 
    }
    
    public function getEnvAsJson() {
        return json_encode((array)$this);
    }
    
    // Set the vCumulus environment 
    private function setEnv($env_name = 'release') {
        $this->env = $env_name;
    }
    
    // Get the channel requested from the URL
    private static function getChannel() {
        $url = \BiStorm\Common::cleanStringForUrl($_SERVER['REQUEST_URI']); // returns the current URL 
        $app = preg_match( '/([c-zC-Z])\/(.*)/', $url, $matches ); // ie '/d/show'
        if( isset($matches[2]) && $matches[2] != '' ) {
            return $matches[2]; // ie 'show'
        }
    }
    
    private static function getChannelActivationUrl() {
        $tv_ip = Common::getSandyTvIp();
        $channel_url = "http://" . $tv_ip . "/slug/iot/hdhr/action/channel?arg1=" . self::getChannel();
        return $channel_url;
    }
    
    private static function getAppId() {
        $url = \BiStorm\Common::cleanStringForUrl($_SERVER['REQUEST_URI']); //returns the current URL
        $app = preg_match( '/([c-zC-Z])/', $url, $matches );
        if(isset($matches[1]) && $matches[1] !== '') {
          return trim($matches[1]);
        }else {
          return false;
        }
    }
    
    private static function getStreamType() {
        $appId = self::getAppId();
        switch ($appId) {
            case 'c': {
                return 'tv';
            }
            case 'd': {
                return 'desktop';
            }
            case 'z': {
                return 'script';
            }
            default: {
                return 'desktop';
            }
        }
    }
    
    private static function getIptvRoot() {
        $tv_ip = Common::getSandyTvIp();
        $url = "http://" . $tv_ip . "/iptv/" . self::getAppId() . '/';
        return $url;         
    }
    
    private static function getIptvUrl() {
        $tv_ip = Common::getSandyTvIp();
        $url = "http://" . $tv_ip . "/iptv/" . self::getAppId() . "/" . self::getChannel() . "/index.m3u8";
        return $url;         
    }
    
    # Get the HdHomerun Ip to serve
    private static function getHdhrIp() {
        $ip = shell_exec(". /vagrant/bistorm/vars/prime_ip");
        $ip = substr($ip, strpos($ip, "@") + 1);

        $ip = preg_replace( "/\r|\n/", "", $ip );

        # Validate ip string before returning
        if(trim($ip) != "") {
            return trim($ip);
        } else {
            return false;
        } 
    }
    
    # Get the LiveStream Ip to serve
    private static function getSandyTvIp() {
        $ip = shell_exec(". /vagrant/bistorm/vars/sandy_ip");
        $ip = substr($ip, strpos($ip, "@") + 1);
        $ip = preg_replace( "/\r|\n/", "", $ip );

        # Validate ip string before returning
        if(trim($ip) != "") {
            return trim($ip);
        } else {
            return false;
        } 
    }
    
    # Get the HdHomerun Channel Linups
    private static function setChannelLineup($force_update = false) {
        $cached_lineup = "/usr/local/bin/bistorm/iot/hdhomerun/lineup.json";
        if( file_exists($cached_lineup) && $force_update == false ) {
            return json_decode(file_get_contents($cached_lineup));
        }
        
        $hdhrIp = self::getHdhrIp();
         // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, "http://" . $hdhrIp . "/lineup.json"); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 
        
        // close curl resource to free up system resources 
        curl_close($ch);  
        
        $obj_response = json_decode($output);
        if( $obj_response == "" ) {
            return null;
        }
        
        foreach ( $obj_response as $channel_name => &$channel_body ) {
            $channel_body->VideoCodec = "MPEG4";
            $channel_body->URL = "http://" . self::getSandyTvIp() . "/slug/iot/hdhr/action/channel?arg1=" . $channel_body->GuideNumber;
        } 
       
        // Write the lineup to the hdhomerun folder
        file_put_contents($cached_lineup, json_encode($obj_response));
        
        return $obj_response;
    
    }
    
    private static function getDashUrl() {
        $tv_ip = Common::getSandyTvIp();
        $url = "http://" . $tv_ip . "/dash/" . self::getAppId() . "/" . self::getChannel() . "/index.mpd";
        return $url;         
    }
    
    private static function getRtmpUrl() {
        $tv_ip = Common::getSandyTvIp();
        $url = "rtmp://" . $tv_ip . "/" . self::getAppId() . "/" . self::getChannel();
        return $url;         
    }
    
    # Calls back to Nginx-RTMP module to start and stop recording of streams
    #  app=[c|d|z]
    #  stream=[channel_id,stream_id]
    #  rec=nginx.conf recorders
    #  action=[start|stop]
    public static function record($app, $stream, $rec, $action) {
        $ip = shell_exec(". /vagrant/bistorm/stream/record " . $app . " " . $stream . " " . $rec . " " . $action);
    }
    
}