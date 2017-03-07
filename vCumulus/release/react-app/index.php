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
    include '/usr/local/bin/bistorm/include/common.php';
    
    /*******
     * Page Vars (currently global)
     */
    $localIP = trim(getSandyIp());
    $tvIP = trim(getSandyTvIP());
    $vcum_env = getVCumEnv();
    $channel_url = "";
    $app_stream = "";
    $hls_dir = "";
    $app_name="d";
    //  Only activate channel if this is a /c/ stream
    if (strpos($_SERVER['REQUEST_URI'], '/c/') !== false) {
        $channel_url = "http://" . $localIP . "/slug/iot/hdhr/action/channel?arg=" . getChannel();
        $app_stream = "tv";
        $app_name = "c";   
    } else if (strpos($_SERVER['REQUEST_URI'], '/d/') !== false) {
        $app_stream = getChannel();
        $app_name = "d"; 
    } else if (strpos($_SERVER['REQUEST_URI'], '/z/') !== false) {
        $app_stream = getChannel();
        $app_name = "z"; 
    }

    #record('start',$app_name, getChannel(), '15s');

?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>BiStorm #ProjectSandy vCumulus 0.4.X</title>
        <meta name="description" content="Peer2Peer, Biz2Biz, Oh what a relief IT is.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-icon.png" />
        <link rel="stylesheet" href="../public/css/normalize.min.css" />
        <script src="//content.jwplatform.com/libraries/LwXnbJyH.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        
        <script>
            <!--
            // TODO: This is a fix to get the channel activated.  
            // Will improve upon it soon.
       
            var app_stream = "<?php echo $app_stream ?>";

            $(document).ready(function () {
                // Set click handlers for Rec buttons
                var frm = $('form[name="frmSave"]');
                
                if(app_stream == 'tv') {                   

                    jQuery.ajax({
                        'url': '<?php print $channel_url ?>',
                        'error': function(xhr, status, e){
                            if(console)console.log("Stream's not here, man. There was an error activating the stream through SLUG or the Gateway took too long to respond.");
                        },
                        timeout: 3000
                    })

                }
            }); 
           -->
        </script>
        
        <style>
            a:hover { text-decoration: none; }
            .main-container h1 { color: white; }
            .main-navigation { font-size: 1.4em; }
            .main-actions { font-size:1.2em; }
            
            .footer-container .wrapper { text-align:center; }
            .footer-container .wrapper .info { color:white; }
            
        </style>
    </head>

    <body style="background-color:black">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using a groundbreakingly <strong>outdated</strong> browser. Why?!? Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">   
            <!-- Headless
                </head></header>
            -->
        </div>

        <div class="main-container">
            <div class="main wrapper clearfix">		
                <div id="stream" width="100%" height="auto">				
                    <!-- for live streaming source -->
                    <video width="100%" height="95%" controls>
                        <src="http://<?php print $localIP ?>/iptv/<?php print $app_name ?>/<?php print getChannel()?>" type="application/x-mpegurl">
                    </video> 
                </div>
            </div>
            <h1 class="vcum-env"><?php print $vcum_env ?></h1>
        </div>
        
        <div class="main-navigation">
            
            <form id="vcumulus-nav" name="navigation">
                <ul>
                    <li><a class="cast-to-device" href="http://<?php print $localIP ?>/iptv/<?php print $app_name ?>/<?php print getChannel()?>" target="_blank">Open IPTV stream for device casting</a></li>
                </ul>
            </form>
        </div>

        <div class="main-actions">
            <form id="slug-action" name="SLUG_Action">
                <ul>
                            <!--<li><input type="submit" value="Rec" name="SLUG_Action_Rec_Start_Raw" /></li>
                    <li><input type="submit" value="Stop" name="SLUG_Action_Rec_Stop_Raw" disabled /></li>-->
                    <li><a class="action-kill-all-feeds" href="http://<?php print $localIP ?>/slug/hdhr/action/stop" target="_blank">Ask Sandy to kill all feeds and conversions</a></li>
                </ul>
            </form>
        </div>
        -->
        <div class="footer-container">
            <footer class="wrapper">
                <div class="info">
                    <p> BiStorm, LLC is a publicly funded services, content and 
                        solutions provider in Tacoma, WA, bolstering our technically and talent-affirming 
                        partners through social and life-enriching mediums.</p>
                    <p> <a href="http://blog.bistorm.org/privacy/" target="_blank">Privacy Policy</a></p>
                    <p> <i>You're actually in our bedroom right now.<br><a href="https://Paypal.Me/BiStormP2P/5" target="_blank">$5 to help keep the lights on?</a></i></p>
                </div>
            </footer>
        </div>

        <!-- The script below is for when we are live streaming -->
        <script>
            var playerInstance = jwplayer("stream");
            playerInstance.setup({
                "image": "http://blog.bistorm.org/wp-content/uploads/2016/08/cropped-bistorm_background.jpg",
                "abouttext": "BiStorm vCumulus and #ProjectSandy support are provided on Twitter through @babelfeed",
                "aboutlink": "http://blog.bistorm.org",
                //setTimeout(function () {}, 5000);
                <!-- <?php # if ($app_stream == "stream") { ?> 
                <?php # } else { ?> -->
                 playlist: [{
                    sources: [
                        {   
                            file: "http://<?php print $tvIP ?>/iptv/<?php print $app_name ?>/<?php print getChannel()?>/index.m3u8"
                        },
                        
                        {
                            file: "http://<?php print $tvIP ?>/dash<?php print $app_name ?>/<?php print getChannel()?>/index.mpd"
                            
                        },
                        {
                            file: "rtmp://<?php print $tvIP ?>/<?php print $app_name ?>/<?php print getChannel()?>"
                        }
                    ]
                }],
                <?php # }  ?>
                "width": "100%",
                "mute": true,
                "preload": "metadata"
            });
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