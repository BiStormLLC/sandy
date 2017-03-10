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
        $this->iptvUrl = self::getIptvUrl();
        $this->dashUrl = self::getDashUrl();
        $this->rtmpUrl = self::getRtmpUrl();
        if($env_name != 'release') {
            $this->env = self::getEnv();
        } 
    }
    
    // Get the vCumulus environment
    public static function getEnv() {
        $output = shell_exec(". /vagrant/bistorm/vars/vcum_env");
        $env = substr($output, strpos($output, "@") + 1);
        if(trim($env) != "") {
            return $env;
        } else {
            return false;
        } 
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
          return $matches[1];
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
    
    private static function getIptvUrl() {
        $tv_ip = Common::getSandyTvIp();
        $url = "http://" . $tv_ip . "/iptv/" . self::getAppId() . "/" . self::getChannel() . "/index.m3u8";
        return $url;         
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
    #  action=[start|stop]
    #  app=[c|d|z]
    #  stream=[channel_id,stream_id]
    #  rec=nginx.conf recorders
    public static function record($action, $app, $stream, $rec) {
         // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, "http://" . getSandyIp() . ":9081/control/record/" . $action . "?app=" . $app . "&stream=" . $stream . "&rec=" . $rec); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 

        // close curl resource to free up system resources 
        curl_close($ch);      
    }
    
}