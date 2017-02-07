<?php
    // 1. This is an alpha version
    // 2. Frustrations build character
    // 3. We're here to help, not to keep busy
    // 4. Comments welcome
    // @EveEqualsMcQ
    
    /*******
     * Includes
     */
    include '/var/www/public/slug/common.php';
    
    /*******
     * Page Vars (currently global)
     */
    $localIP = $_SERVER['SERVER_ADDR'];
    $localPort = $_SERVER['SERVER_PORT'];
    $channel_url = "http://" . $localIP . ":9082/c/" . getChannel();
?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>BiStorm #ProjectSandy vCumulus 0.3.0</title>
        <meta name="description" content="Peer2Peer, Biz2Biz, Oh what a relief IT is.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-icon.png" />
        <link rel="stylesheet" href="../public/css/normalize.min.css" />
        <script src="//content.jwplatform.com/libraries/LwXnbJyH.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        
        <script>
            // TODO: This is a fix to get the channel activated.  
            // Will improve upon it soon.
            jQuery.ajax({
                'url': '<?php print $channel_url ?>',
                'complete': function(xhr, status){},
                'error': function(xhr, status, e){
                    if(console)console.log("Stream's not here, man. There was an error activating the stream through SLUG.");
                }
            });
        </script>
    </head>

    <body style="background-color:black">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using a groundbreakingly <strong>outdated</strong> browser. Why?!? Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">   
            </header>
        </div>

        <div class="main-container">
            <div class="main wrapper clearfix">		
                <div id="stream" width="100%" height="auto">				
                    <!-- for live streaming source -->
                    <video width="100%" height="95%" controls>
                        <src="http://<?php print $localIP ?>:9081/<?php print getChannel()?>.m3u8" type="application/x-mpegurl">
                    </video> 
                </div>
            </div>
        </div> 
        <div class="footer-container">
            <footer class="wrapper">
                <div class="info">

                </div>
            </footer>
        </div>
vagr
        <!-- The script below is for when we are live streaming -->
        <script>
            var playerInstance = jwplayer("stream");
            playerInstance.setup({
                //setTimeout(function () {}, 5000);
                playlist: [{
                        sources: [
                            {
                                file: "http://<?php print $localIP ?>:9081/<?php print getChannel()?>.m3u8",
                            },
                            {
                                file: "rtmp://<?php print $localIP ?>:1981/c/<?php print getChannel()?>",
                            }
                        ]
                    }],
                width: "100%",
                aspectratio: "16:9",
                hlslabels: {
                    "2500": "High",
                    "1000": "Medium"
                }
            });
        </script>

        <a href="http://<?php print $localIP ?>:9082/action/stop" style="font-size:1.8em" target="_blank">Ask Sandy to kill the feed</a>
        
        <!-- Google Analytics -->
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
</html>