<?php

function cleanForUrl($text) {
    return preg_replace('[^a-z^A-Z^0-9^-]', "-", $text);
}

function getSandyIp() {
    $output = shell_exec(". /vagrant/bistorm/vars/sandy_ip");
    $ip = substr($output, strpos($output, "@") + 1);    
    return $ip;
}

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


