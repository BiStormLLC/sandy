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
            return $ip;
        } else {
            return false;
        } 
    }

    # Get the LiveStream Ip to serve
    public static function getSandyTvIp() {
        $env = self::getVCumEnvName();
        if( trim($env) == "stg-ext" ) {
            return "sandy1.bistorm.us";
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
        foreach( $slug_required_apps as $app_name => $app_body ) {
            $ux_env->slug->{$app_name} = new \BiStorm\SLUG\Slug('bistorm', $app_body);
        }
    }
    
    # Usecase: Redirect to an existing conversion channel if one is running in the 'c' app 
    # $app_name = ['c','d'...'z']
    public static function getLiveStreamUrls($app_name) {
        // SLUG integration
        if( ! $this->isSlugEnabled() ) {
            throw new Exception('SLUG is not enabled for this environment.');
        }
        
    }

    # SLUG integration to determine if NGINX is currently serving RTMP content
    public static function isSandyStreaming() {
        // SLUG integration
        if( ! $this->isSlugEnabled() ) {
            throw new Exception('SLUG is not enabled for this environment.');
        }
    }

    # SLUG integration to determine if FFMPEG processes are running
    public static function isSandyConverting() {
        // SLUG integration
        if( ! $this->isSlugEnabled() ) {
            throw new Exception('SLUG is not enabled for this environment.');
        }
    } 
    
    # Is SLUG enabled
    public function isSlugEnabled() {
        if ( is_object($this->slug) ) {
            return true;
        }
        return false;
    }
}