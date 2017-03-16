<?php
    namespace BiStorm;

    // 1. This is an alpha version
    // 2. Frustrations build character
    // 3. We're here to help, not to keep busy
    // 4. Comments welcome
    // @EveEqualsMcQ
    
    /*******
     * Includes
     */
    include_once '/usr/local/bin/bistorm/include/common.php';
    include_once '/usr/local/bin/bistorm/include/vcumulus.php';
    
    $slug_required_apps = array(
        "hdhr_channel" => array(
            'app_namespace' => 'iot', 
            'app_name' => 'hdhr',
            'action' => 'channel',
            'args' => array(),
        ),
        "hdhr_stop" => array(
            'app_namespace' => 'iot',
            'app_name' => 'hdhr',
            'action' => 'stop',
            'args' => array(),
        ),
        'set_flag' => array(
            'app_namespace' => 'vcumux',
            'app_name' => 'stream',
            'action' => 'set_flag',
            'args' => array(),
        ),
        'check_flag' => array(
            'app_namespace' => 'vcumux',
            'app_name' => 'flags',
            'action' => 'check_flag',
            'args' => array(),
        ),
        'set_stream_vars' => array(
            'app_namespace' => 'vcumux',
            'app_name' => 'stream',
            'action' => 'set_active_stream_vars',
            'args' => array(),
        ),
        'record' => array(
            'app_namespace' => 'vcumux',
            'app_name' => 'stream',
            'action' => 'record',
            'args' => array(),
        )
    );
    
    /**
     * All our packaged goodies
     */
    $storm = new Common();
    $env = $storm->getVCumEnvName();
    $storm->setVCumEnv($env, $storm->path); // $storm->vCumulus
    $storm->addSlugToEnv($storm->vCumulus, $slug_required_apps);
    
    $storm->vCumulus->active_streams = $storm->getLiveStreamUrls();
    $storm->vCumulus->open_channels = $storm->getOpenChannels();
    
    if( ! empty($storm->vCumulus->open_channels['hls']) ) {
        $active_conversion = $storm->vCumulus->open_channels['hls'][0];
        if( $storm->vCumulus->appId == 'c' && strpos($active_conversion, "c/") !== FALSE ) {
            if ( $storm->vCumulus->appId . "/" . $storm->vCumulus->channel != $active_conversion ) {
                header("Location: " . "http://" . $storm->getSandyTvIp() . "/" . $storm->vCumulus->open_channels['hls'][0]);
                die();
            }
        }  
    }

?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>BiStorm #ProjectSandy vCumulus 0.5.X</title>
        <meta name="description" content="Peer2Peer, Biz2Biz, Oh what a relief IT is.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-icon.png" />
        <link rel="stylesheet" href="../public/css/normalize.min.css" />
        <script src="//content.jwplatform.com/libraries/LwXnbJyH.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <script type="text/javascript">
            <!--
            
            var app_stream = "<?php print $storm->vCumulus->streamType ?>";
            var vcum = {"ux":<?php print $storm->vCumulus->getEnvAsJson() ?>};
            
            jQuery(document).ready(function () {
                var slug = {
                    'response': '',
                    'status': '',
                    'app_url': '',
                    'actions': {},
                    'buttons': [],
                    'args': "",
                    'load_app_url': function( app_url, args ) {
                        if(typeof args != 'undefined' && typeof args[0] != 'undefined') {
                            var arg_str = "?";
                            var i = 1;
                            for(arg in args) {
                                arg_str += 'arg' + i + "=" + args[arg] + "&";
                                i++;
                            }
                        } else {
                            arg_str = "";
                        }
                        this.app_url = app_url + arg_str;
                    },
                
                    'call': function( callback ) {

                        jQuery.ajax({
                            'url': this.app_url,
                            'cache': false,
                            'timeout': 3000,
                            'success': function(data, status, xhr) {
                                if(typeof data.slug != 'undefined' && data.slug.msg != 'undefined') {
                                    slug.response = data.slug.msg;
                                }
                                if( typeof slug.response.error != "undefined") {
                                    slug.status = '500';
                                    slug.response = slug.app_url + " call failed with status " + slug.status + ": " + decodeURIComponent(slug.response.error).replace(/\+/g,' ');
                                    if(console)console.log(slug.response);
                                } else if (typeof slug.response.SLUG != "undefined") {
                                    slug.status = '200';
                                    slug.response = slug.app_url + " call succeeded with status " + slug.status + ": " + decodeURIComponent(slug.response.SLUG).replace(/\+/g, ' ');
                                    if(console)console.log(slug.response);
                                }
                                if (typeof callback == 'function') callback();
                            },
                            'error': function(xhr, status, e){
                                slug.response = "SLUG call failed.";
                                slug.status = "error";
                            },
                            timeout: 3000
                        });
                    }  
                };
                
                slug.buttons = jQuery("input[slug^='SLUG']");
                jQuery(slug.buttons).each(function(){
                    var url_parts = jQuery(this).attr('slug').split('__')[0].split('_');
                    var action = jQuery(this).attr('slug').split('__')[1];
                    if ( typeof url_parts !== 'undefined' && typeof url_parts[1] !== 'undefined' ) {
                        for (part in url_parts) {
                            part = part.toLowerCase();
                        }
                        var argsStr = jQuery(this).attr('args');
                        if ( typeof argsStr != 'undefined' ) {
                           args = argsStr.split(',');
                        }
                        var url = '/' + url_parts.join('/').toLowerCase() + '/action/' + action.toLowerCase();
                        slug.actions[this.name] = url;
                        if( typeof args != 'undefined' && typeof args[0] != 'undefined') {
                            qs = '?';
                            for( var i=0; i < args.length; i++ ) {
                                if(i == 0) {
                                    qs += 'arg' + (i+1) + '=' + args[i];
                                } else {
                                    qs += '&arg' + (i+1) + '=' + args[i];
                                }   
                            }
                        } else {
                            qs = '';
                        }
                        url += qs;
                        if(console) console.log( 'SLUG attached to ' + this.name + ': ' + url);
                        jQuery(this).unbind().on('click', function(){
                            slug.load_app_url(url);
                            slug.call();
                        });
                    }
                });
                
                // GENERIC CONTROL OF SELECTS
                slug.selects = jQuery("select[slug^='SLUG']");
                jQuery(slug.selects).each(function(){
                    jQuery(this).unbind().change(function(){
                        var option = jQuery("option:selected", this);
                        var url = jQuery(this).attr('url');
                        slug.load_app_url(url);
                        slug.call();
                    });
                });
                
                // Set the channel selector to the channel requested in the url
                jQuery("select[name=SLUG_IOT_HDHR__channel]").val(vcum.ux.channel);

                // Request the selected channel using SLUG
                slug.load_app_url( vcum.ux.channelActivationUrl );
                slug.call();  
                
                // CHANNEL SELECTION ACTIONS OVERRIDES GENERIC CONTROLS
                jQuery("select[slug=SLUG_IOT_HDHR__channel]").unbind().on('click', 
                    function() {
                        slug.load_app_url("http://" + vcum.ux.server_name + "/slug" + vcum.ux.slug.hdhr_stop.path);
                        slug.call();
                        
                    }
                ).on('change', function(){
                    var option = jQuery("option:selected", this);
                    window.location = option.attr('url');
                });
                
                // REC BUTTON ACTIONS
                jQuery("input[name=Start]").on('click', 
                    function() {
                        jQuery("input[name=Start]").attr('disabled',true);
                        jQuery("input[name=Stop]").removeAttr('disabled');
                    }
                );
                
                jQuery("input[name=Stop]").on('click', 
                    function() {
                        jQuery("input[name=Stop]").attr('disabled',true);
                        jQuery("input[name=Start]").removeAttr('disabled');
                    }
                );
                
                if(console) {
                    console.log(vcum);
                    console.log(slug);
                }
                
            }); 
           -->

        </script>
        
        <style>
            a:hover { text-decoration: none; }
            .main { color: white; }
            .header { height: 0px; }
            .container h1 { color: white; font-size:1.0em; }
            .navigation { font-size: 0.9em; }
            .actions { font-size:1.2em; }
            .vcum-btn { color: black; }
            .vcum-btn:disabled { color: gray; }
            .vcum-sel { color: black; width: 50%; }

            .killswitch {  }
            
            .footer-container .wrapper { text-align: center; padding: 20px; }
            .footer-container .wrapper .info { color: white; }
            .footer-container .wrapper .paypal { color: white; }
            .vcum-col-3 {
                width: 100%;
            }
            .vcum-col-3 > ul {
                padding: 0 1% 0 9%;
                margin: 0px;
            }
            
            .vcum-col-3 > ul > li {
                display: inline-block;
                vertical-align: top;
                width: 30%;
                box-sizing: border-box;
                text-align: center;
                padding: 8px 0;
            }
            
            @media screen and (max-width: 960px) {

            }
            
        </style>
    </head>

    <body style="background-color:black">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using a groundbreakingly <strong>outdated</strong> browser. Why?!? Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="main container">
            <div class="main wrapper clearfix">		
                <div id="stream" width="100%" height="auto">				
                    <!-- for live streaming source -->
                    <video width="100%" height="95%" controls>
                        <src="<?php print $storm->vCumulus->iptvUrl ?>" type="application/x-mpegurl">
                    </video> 
                </div>
            </div>
            <h1 class="vcum-env"><?php print $storm->vCumulus->env ?></h1>
        </div>
        
        <div class="main navigation">
            <form id="vcumulus-nav" name="navigation">
                <ul>
                    <?php
                        if( ! empty( $storm->vCumulus->active_streams) ) {
                           foreach( $storm->vCumulus->active_streams as $stream_url ) {
                                print "<li>Open <a target=\"_blank\" href='" . $stream_url . "'>" . $stream_url . "</a> for device casting.</li>";
                           }
                        } else {
                           print "<li>Open <a target=\"_blank\" href='" . $storm->vCumulus->iptvUrl . "'>" . $storm->vCumulus->iptvUrl . "</a> for device casting.</li>";
                        }
                    ?>
                </ul>
            </form>
        </div>

        <div class="vcum main interface">
            <form id="slug-action" name="slug_actions">
                <div class="vcum-col-3">
                    <ul class="">
                        <li>
                            <span>Record to X^nDir</span><br>
                            <input class="vcum-btn rec start" type="button" value="Rec" args="<?php print 'start' . ',' . $storm->vCumulus->appId . ',' . $storm->vCumulus->channel?>" slug="SLUG_VCUMUX_STREAM__record" name="Start" />
                            <input class="vcum-btn rec stop" type="button" value="Stop" args="<?php print 'stop' . ',' . $storm->vCumulus->appId . ',' . $storm->vCumulus->channel?>" slug="SLUG_VCUMUX_STREAM__record" name="Stop"  disabled />
                            <select class="vcum-sel" name="Profile">
                                                                                
                            </select>
                        </li>
                        <li>
                            <span>Change to Channel...</span><br>
                            <select class="vcum-sel channel" slug="SLUG_IOT_HDHR__channel" name="Channel">
                                <option default value="0">Channel</option>
                                <?php
                                    foreach ( $storm->vCumulus->channel_lineup as $channel ) {
                                        $iptvUrl = "http://" . $storm::getSandyTvIp() . "/c/" . $channel->GuideNumber;
                                        echo "<option label=\"" . $channel->GuideNumber . ' > ' . $channel->GuideName . "\" name=\"" . $channel->GuideNumber . "\" url=\"" . $iptvUrl . "\" action=\"" . $channel->URL . "\" value=\"" . $channel->GuideNumber . "\">" . $channel->GuideNumber . "</option>";
                                    }
                                ?>
                            </select>
                        </li>
                        <li><span>Ask Sandy to</span><br>
                            <input class="vcum-btn killswitch" type="button" value="Kill All Feeds" slug="SLUG_IOT_HDHR__stop" name="KillSwitch"></li>
                    </ul>
                </div>
            </form>
        </div>

        <div class="footer-container">
            <footer class="wrapper">
                <div class="info">
                    <p> BiStorm, LLC is a publicly funded services, content and 
                        solutions provider in Tacoma, WA, bolstering our technically and talent-affirming 
                        partners through social and life-enriching mediums.</p>
                    <p> <a href="http://blog.bistorm.org/privacy/" target="_blank">Privacy Policy</a></p>
                </div>
                <p><a class="paypal" href="https://Paypal.Me/BiStormP2P/5" target="_blank">$5 to help keep the lights on?</a></p>   
            </footer>
        </div>

        <script>
            loadPlayer = function( playlist ) {
                if ( typeof playlist == 'undefined') {
                    playlist = [{
                        sources: [
                            {   
                                'label': 'iptv',
                                'file': "<?php print $storm->vCumulus->iptvUrl ?>"
                            },
                            {
                                'label': 'dash',
                                'file': "<?php print $storm->vCumulus->dashUrl ?>"

                            },
                            {
                                'label': 'flash',
                                'file': "<?php print $storm->vCumulus->rtmpUrl ?>"
                            }
                        ]
                    }]
                } 
                player = jwplayer("stream");
                player.setup({
                    "image": "http://blog.bistorm.org/wp-content/uploads/2016/08/cropped-bistorm_background.jpg",
                    "abouttext": "BiStorm vCumulus and #ProjectSandy support are provided on Twitter through @babelfeed",
                    "aboutlink": "http://blog.bistorm.org",
                    "playlist": playlist,
                    "width": "100%",
                    "mute": true,
                    "preload": "metadata"
                });
            }();
            jQuery(document).ready(function() {
                vcum.ux.player = player;
            })
        </script>

        <!-- Google Analytics -->
        <!-- Delete this block if you don't want to share diagnostic info with BiStorm -->
        <script>
                    (function (b, o, i, l, e, r) {
                        b.GoogleAnalyticsObject = l;
                        b[l] || (b[l] =
                                function () {
                                    (b[l].q = b[l].q || []).push(arguments)
                                });
                        b[l].l = +new Date;
                        e = o.createElement(i);
                        r = o.getElementsByTagName(i)[0];
                        e.src = '//www.google-analytics.com/analytics.js';
                        r.parentNode.insertBefore(e, r)
                    }(window, document, 'script', 'ga'));
                    ga('create', 'UA-79492414-1', 'auto');ga('send', 'pageview');
        </script>
    </body>
</html>: