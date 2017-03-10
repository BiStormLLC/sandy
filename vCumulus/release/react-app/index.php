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
            'app_name' => 'stream',
            'action' => 'check_flag',
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
        
        <script>
       
            var app_stream = "<?php echo $storm->vCumulus->streamType ?>";

            $(document).ready(function () {
                // Set click handlers for Rec buttons
                var frm = $('form[name="frmSave"]');
                
                if(app_stream == 'tv') {                   

                    jQuery.ajax({
                        'url': '<?php print $storm->vCumulus->channelActivationUrl ?>',
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
                        <src="<?php print $storm->vCumulus->iptvUrl ?>" type="application/x-mpegurl">
                    </video> 
                </div>
            </div>
            <h1 class="vcum-env"><?php print $storm->vcumulus->env ?></h1>
        </div>
        
        <div class="main-navigation">
            <form id="vcumulus-nav" name="navigation">
                <ul>
                    <li><a class="cast-to-device" href="<?php print $storm->vCumulus->iptvUrl ?>" target="_blank">Open IPTV stream for device casting</a></li>
                </ul>
            </form>
        </div>

        <div class="vcum main interface">
            <form id="slug-action" name="SLUG_Action">
                <div class="apps">
                    <div class="c">
                        <ul class="vcum-col-3">
                            <li><input class="vcum-btn rec start" type="submit" value="Rec" name="SLUG_IOT_HDHR_Action_Rec_Start_Raw" /></li>
                            <li><input class="vcum-btn rec stop" type="submit" value="Stop" name="SLUG_IOT_HDHR_Action_Rec_Stop_Raw" disabled /></li>
                            <li>Ask Sandy to <input class="vcum-btn sandy killswitch" type="submit" value="Kill All Feeds" name="SLUG_ACTION_FFMPEG_KILL"></li>
                        </ul>
                    </div>
                </div>
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
                playlist: [{
                    sources: [
                        {   
                            file: "<?php print $storm->vCumulus->iptvUrl ?>"
                        },
                        
                        {
                            file: "<?php print $storm->vCumulus->dashUrl ?>"
                            
                        },
                        {
                            file: "<?php print $storm->vCumulus->rtmpUrl ?>"
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