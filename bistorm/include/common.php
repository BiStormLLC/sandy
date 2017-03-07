<?php
namespace BiStorm;

#  Ensures that the string is acceptable for URL use
function cleanForUrl($text) {
    return preg_replace('[^a-z^A-Z^0-9^-]', "-", $text);
}

// Get the vCumulus environment
function getVCumEnv() {
    $output = shell_exec(". /vagrant/bistorm/vars/vcum_env");
    $env = substr($output, strpos($output, "@") + 1);
    if(trim($env) != "") {
        return $env;
    } else {
        return false;
    } 
}

// Get the network address stored in the vars directory
function getSandyIp() {
    $env = getVCumEnv();
    if( trim($env) == "release" ) {
        return "sandy.bistorm.us";
    } else {
        echo getVCumEnv();
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

# Get the LiveStream IP to serve
function getSandyTvIP() {
   $env = getVCumEnv();
    if( trim($env) == "release" ) {
        return "sandy.bistorm.us";
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

# URL parser to get a channel number for playback
function getChannel() {
    $url = cleanForUrl($_SERVER['REQUEST_URI']); //returns the current URL
    $parts = explode('/',$url);
    $dir = $_SERVER['SERVER_NAME'];

    for ($i = 0; $i < count($parts) - 1; $i++) {
        $dir .= $parts[$i] . "/";
    }

    # TODO: This block will be more fleshed out later
    #   The intent is to ensure string quality of the url upon entry to the page
    #   (Security: Avoiding string injections)
    if ($pos = strpos($url, 'c/') !== false) {
        $hd_ch = substr($url, $pos);
        $hd_ch = trim($hd_ch, 'c/');
        $hd_ch = strval(floatval($hd_ch));
        return $hd_ch;
    } elseif ($pos = strpos($url, 'd/') !== false) {
        $hd_ch = substr($url, $pos);
        $hd_ch = trim($hd_ch, 'd/');
        return $hd_ch;
    } elseif ($pos = strpos($url, 'z/') !== false) {
        $hd_ch = substr($url, $pos);
        $hd_ch = trim($hd_ch, 'z/');
        return $hd_ch;
    } 
}

# Callback to Nginx-RTMP module to start and stop record streams
#  action=[start|stop]
#  app=[c|d|z]
#  stream=[channel_id,stream_id]
#  rec=nginx.conf recorders
function record($action, $app, $stream, $rec) {
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


