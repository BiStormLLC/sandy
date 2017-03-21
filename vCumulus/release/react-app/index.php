<?php
    namespace BiStorm;
    
    error_reporting(E_ERROR);

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
            'app_name' => 'flags',
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
        ),
        'log_view' => array(
            'app_namespace' => 'vcumux',
            'app_name' => 'log',
            'action' => 'log_view',
            'args' => array(),
        ),
        'profiles' => array(
            'app_namespace' => 'vcumux',
            'app_name' => 'stream',
            'action' => 'list_profiles',
            'args' => array(),
        ),
        'set_rec_profile' => array(
            'app_namespace' => 'sandy',
            'app_name' => 'xndir',
            'action' => 'set_rec_profile_var',
            'args' => array(),
        ),
        'send_to_vod' => array(
            'app_namespace' => 'sandy',
            'app_name' => 'xndir',
            'action' => 'raw_mp4_to_vod',
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

    $storm->vCumulus->clients = $storm->getClients($storm->vCumulus->appId);
    $storm->vCumulus->rec_profiles = $storm->getXnDirProfiles();
    
    $storm->vCumulus->active_streams = $storm->getLiveStreamUrls();
    $storm->vCumulus->open_channels = $storm->getOpenChannels();
    
    // Request the selected channel using SLUG server-side
    $storm->vCumulus->slug->hdhr_channel->addActionArg('1', $storm->vCumulus->channel);
    $storm->vCumulus->slug->hdhr_channel->exec(false);
    
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
        <link rel="stylesheet" href="../../public/css/normalize.min.css" />
        <script src="//content.jwplatform.com/libraries/LwXnbJyH.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <script type="text/javascript">
            <!--
            
            var app_stream = "<?php print $storm->vCumulus->streamType ?>";
            var vcum = {"ux":<?php print $storm->vCumulus->getEnvAsJson() ?>};
            
            jQuery(document).ready(function () {
            
                vcum.ux.slug.interactions = {
                    'response': '',
                    'status': '',
                    'app_url': '',
                    'actions': {},
                    'buttons': [],
                    'load_app_url': function ( elm, args ) {
                        this.current_app_elm = elm;
                        var url_parts = jQuery(elm).attr('slug').split('__')[0].split('_');
                        var action = jQuery(elm).attr('slug').split('__')[1];
                        if ( typeof url_parts !== 'undefined' && typeof url_parts[1] !== 'undefined' ) {
                            for (part in url_parts) {
                                part = part.toLowerCase();
                            }
                            if ( typeof args != 'undefined' && typeof args == 'string' ) {
                               args = args.split(',');
                            }
                            var url = '/' + url_parts.join('/').toLowerCase() + '/action/' + action.toLowerCase();
                            var name = jQuery(elm).attr('name');
                            vcum.ux.slug.interactions.actions[name] = url;
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
                            this.app_url = url;
                        }
                    },
                
                    'call': function( callback ) {

                        jQuery.ajax({
                            'url': this.app_url,
                            'cache': false,
                            'timeout': 3000,
                            'success': function(data, status, xhr) {
                                if(typeof data.slug != 'undefined' && data.slug.msg != 'undefined') {
                                    vcum.ux.slug.interactions.response = data.slug.msg;
                                }
                                if( typeof vcum.ux.slug.interactions.response.error != "undefined") {
                                    vcum.ux.slug.interactions.status = '500';
                                    vcum.ux.slug.interactions.response = vcum.ux.slug.interactions.app_url + " call failed with status " + vcum.ux.slug.interactions.status + ": " + decodeURIComponent(vcum.ux.slug.interactions.response.error).replace(/\+/g,' ');
                                    if(console)console.log(vcum.ux.slug.interactions.response);
                                } else if (typeof vcum.ux.slug.interactions.response.SLUG != "undefined") {
                                    vcum.ux.slug.interactions.status = '200';
                                    vcum.ux.slug.interactions.response = vcum.ux.slug.interactions.app_url + " call succeeded with status " + vcum.ux.slug.interactions.status + ": " + decodeURIComponent(vcum.ux.slug.interactions.response.SLUG).replace(/\+/g, ' ');
                                    if(console)console.log(vcum.ux.slug.interactions.response);
                                }
                                if (typeof callback == 'function') callback(vcum.ux.slug.interactions.current_app_elm);
                            },
                            'error': function(xhr, status, e){
                                vcum.ux.slug.interactions.response = "SLUG call failed.";
                                vcum.ux.slug.interactions.status = "error";
                            },
                            timeout: 5000
                        });
                    }  
                };
                
                // GENERIC CONTROL OF BUTTONS
                jQuery("input[slug^='SLUG']").each(function() {
                    vcum.ux.slug.interactions.buttons.push(this);        
                });
                jQuery(vcum.ux.slug.interactions.buttons).each(function(){
                    jQuery(this).unbind().on('click', function(){
                        vcum.ux.slug.interactions.load_app_url(this, jQuery(this).attr('args'));
                        vcum.ux.slug.interactions.call();
                    });
                    if(console) console.log( 'SLUG attached to ' + jQuery(this).attr('name') + ': ' + jQuery(this).attr('slug'));
                });
                
                // GENERIC CONTROL OF SELECTS
                vcum.ux.slug.interactions.selects = jQuery("select[slug^='SLUG']");
                jQuery(vcum.ux.slug.interactions.selects).each(function(){
                    jQuery(this).unbind().change(function(){
                        var option = jQuery("option:selected", $(this));
                        var args_str = jQuery(option).attr('args');
                        vcum.ux.slug.interactions.load_app_url(this, args_str);
                        vcum.ux.slug.interactions.call();
                    });
                    if(console) console.log( 'SLUG attached to ' + jQuery(this).attr('name') + ': ' + jQuery(this).attr('slug'));
                });
                
                // Set the channel selector to the channel requested in the url
                for( var i=0; i < vcum.ux.channel_lineup.length; i++ ) {
                    if ( vcum.ux.channel_lineup[i].GuideNumber == vcum.ux.channel) {
                        jQuery("select[slug=SLUG_IOT_HDHR__channel]").prop('selectedIndex', i+1);
                        break;
                    }
                } 
                
                // CHANNEL SELECTION ACTIONS ADDS ONTO GENERIC ONCHANGE CONTROLS
                jQuery("select[slug=SLUG_IOT_HDHR__channel]").unbind().on('change', 
                    function(){
                        channelSelect = jQuery("select[slug=SLUG_IOT_HDHR__channel]");
                        var killswitch = jQuery("#slugAction #killSwitch");
                        vcum.ux.slug.interactions.load_app_url(killswitch, '');
                        vcum.ux.slug.interactions.call(function(elm){
                            var option = jQuery("option:selected", channelSelect);
                            var args_str = jQuery(option).attr('args');
                            vcum.ux.slug.interactions.load_app_url(channelSelect, args_str);
                            vcum.ux.slug.interactions.call(function(elm){
                                var opt = jQuery("option:selected", elm);
                                window.location = opt.attr('url');
                            });
                        });
                    }
                );
                
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
                    console.log('vcum :');
                    console.log(vcum);
                    console.log('slug interactions: ');
                    console.log(vcum.ux.slug.interactions);
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
            <form id="vcumulusNav" name="navigation">
                <ul>
                    <?php
                        if( ! empty( $storm->vCumulus->active_streams) ) {
                           foreach( $storm->vCumulus->active_streams as $stream_url ) {
                                if( strpos($stream_url, "/iptv/") !== FALSE) {
                                     print "<li>Open <a target=\"_blank\" href='" . $stream_url . "'>" . $stream_url . "</a> for device casting.</li>";
                                }
                           }
                        } else {
                           print "<li>Open <a target=\"_blank\" href='" . $storm->vCumulus->iptvUrl . "'>" . $storm->vCumulus->iptvUrl . "</a> for device casting.</li>";
                        }
                    ?>
                    <li>Click! <a href="https://www.clickcabletv.com/guide/" target="_blank">Cable Guide</a> </li>
                    <li>FFmpeg <a href="http://<?php print $storm->vCumulus->server_name ?>/slug/sandy/bistorm/action/ffmpeg_status" target="_blank">Status</a> </li>
                </ul>
            </form>
        </div>

        <div class="vcum main interface">
            <form id="slugAction" name="slug_actions">
                <div class="vcum-col-3">
                    <ul class="">
                        <li>
                            <span>Record to X^nDir</span><br>
                            <input class="vcum-btn rec stop" type="button" value="Stop" args="<?php print 'stop' . ',' . $storm->vCumulus->appId . ',' . $storm->vCumulus->channel?>" slug="SLUG_VCUMUX_STREAM__record" name="Stop"  disabled />
                            <input class="vcum-btn rec start" type="button" value="Rec" args="<?php print 'start' . ',' . $storm->vCumulus->appId . ',' . $storm->vCumulus->channel?>" slug="SLUG_VCUMUX_STREAM__record" name="Start" />
                            <input class="vcum-btn rec delete" type="button" value="New Recording" args="<?php print $storm->vCumulus->channel?>" slug="SLUG_SANDY_XNDIR__delete_temp_flv" name="Delete" />     
                            </br><br>
                            <span>Transcoding Profiles</span></br>
                            <select class="vcum-sel" name="Profile" slug="SLUG_VCUMUX_STREAM__set_rec_profile_var">
                                <option args="raw" url="<?php echo "http://" . $storm->vCumulus->server_name . "/slug/vcumux/stream/set_rec_profile_var?arg1=copy" ?>" label="save to temp" name="raw" value="raw">Save to Temp</option>
                                <?php
                                    foreach($storm->vCumulus->rec_profiles as $profile) {
                                       $set_profile_url = "http://" . $storm->vCumulus->server_name . "/slug/vcumux/stream/set_rec_profile_var";
                                       echo "<option args=\"" . $profile . "\" url=\"" . $set_profile_url . "\" label=\"" . $profile . "\" name=\"" . $profile . "\" value=\"" . $profile . "\">" . $profile . "</option>";
                                    }       
                                ?>                                          
                            </select>
                            <!-- //Unhide to manually force X^nDir to recognize recording profiles: <input disabled id="convert" class="vcum-btn rec convert" type="button" value="Convert" args="" slug="SLUG_SANDY_XNDIR__raw_mp4_to_vod" name="Convert" />  --> 
                            </li>
                        <li>
                            <span>Change to Channel...</span><br>
                            <select class="vcum-sel channel" slug="SLUG_IOT_HDHR__channel" name="Channel">
                                <option default value="0">Channel</option>
                                <?php
                                    foreach ( $storm->vCumulus->channel_lineup as $channel ) {
                                        $iptvUrl = "http://" . $storm::getSandyTvIp() . "/c/" . $channel->GuideNumber;
                                        echo "<option args=\"" . $channel->GuideNumber . "\" name=\"" . $channel->GuideNumber . "\" url=\"" . $iptvUrl . "\" action=\"" . $channel->URL . "\" value=\"" . $channel->GuideNumber . "\">" . $channel->GuideNumber . ' > ' . $channel->GuideName . "</option>";
                                    }
                                ?>
                            </select>
                        </li>
                        <li><span>Ask Sandy to</span><br>
                            <div>
                                <?php
                                    foreach($storm->vCumulus->clients as $client) {
                                        echo "<input class=\"vcum-btn killswitch\" args=\"" . $storm->vCumulus->appId . "," . $storm->vCumulus->channel . "," . $client['ip'] . "\" type=\"button\" value=\"Drop " . $client['ip'] . "\" slug=\"SLUG_VCUMUX_STREAM__drop_client\" name=\"DropClient\"/><br>";
                                    }
                                ?>
                                <input class="vcum-btn killswitch" type="button" value="Kill All Feeds" args="" slug="SLUG_IOT_HDHR__stop" name="KillSwitch" id="killSwitch"/>
                            </div>
                       </li>
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
            jQuery(document).ready(function() {
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
                    var player = jwplayer("stream");
                    player.setup({
                        "image": "http://blog.bistorm.org/wp-content/uploads/2016/08/cropped-bistorm_background.jpg",
                        "abouttext": "BiStorm vCumulus and #ProjectSandy support are provided on Twitter through @babelfeed",
                        "aboutlink": "http://blog.bistorm.org",
                        "playlist": playlist,
                        "width": "100%",
                        "mute": true
                    });
                    vcum.ux.player = player;
                }();
                var t;
                var timer=3000;
                vcum.ux.player.onIdle(function() {
                    t=setTimeout("vcum.ux.player.play()",timer);
                });
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