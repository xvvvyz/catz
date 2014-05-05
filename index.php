
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="catz catz catz catz catz catz catz catz catz OMG">
        <meta name="viewport" content="width=device-width, user-scalable=no">
        
        <title>Catz</title>

        <link rel="icon" type="image/png" href="favicon.ico">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    <body>
        <noscript>
            <p class="browsehappy">JavaScript must be enabled for this site to do anything.</p>
        </noscript>
        
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div id="progress">
            <div class="bar"></div>
        </div>

        <div id="everything">
            <main id="main">
                <form id="main_form" method="get" name="main_form">
                    <?php
                        $default = (isset($_GET["q"]) ? $_GET["q"] : "");
                        echo '<input id="main_text" type="text" value="'.$default.'">'."\n";
                    ?>
                    <input id="main_button" type="submit" value="">
                </form>

                <div id="message"></div>

                <div id="hidden">
                    <!-- ALL -->
                    <span id="content_title"></span>
                    <span id="timer"></span>
                    <span id="domain"></span>
                    <span id="url"></span>
                    <span id="track_count"></span>
                    <span id="total_tracks"></span>
                    <span id="download_count"></span>
                    <span id="wait_duration">0</span>
                    <span id="recursive_downloads">0</span>
                    <span id="slug"></span>

                    <!-- 8TRACKS -->
                    <span id="play_token"></span>
                    <span id="mix_id"></span>
                    <a id="results_cover_tag"></a>

                    <!-- SONGZA -->
                    <span id="session_id"><?php echo substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 18); ?></span>
                    <span id="station_id"></span>
                </div>

                <div class="popup" id="about_div">
                    <a class="close_popup">x</a>
                    <h2>OH FAQ!</h2>
                    <b>What Is This?</b><p>This is a cat website (made for cats... like you). With the internet came an unruley amount of cats and cat like things. Maybe the Egyptians understood something we still don't quite understand. Along with cats, this website provides you with a whole bunch of music. Music to the eyes, that is, in the form of cats.</p><br><br>
                    <b>Wait.. Why?</b><p>Because of <a href="http://www.youtube.com/v/xEhaVhta7sI">this</a>... And because obsessively viewing cat pictures while enjoying my favorite music can turn a bad day into something slightly better. I hope you can find joy from this website, too.</p><br><br>
                    <b>How can I thank you?</b><p>You can drop me a line by <a href="mailto:admin@omgcatz.com">email</a>, and you can click <a href="#donate">here</a> if you are interested in donating.</p><br><br>
                </div>

                <div class="popup" id="donate_div">
                    <a class="close_popup">x</a>
                    <h2>Helllp</h2>
                    <p>This website gets too much traffic, and, due to special kitties, it requires a fairly strong (expensive) server to keep it from crashing. Thus, ads and donations are its lifeblood. I promise not to spend your contribution on drugs and hookers.</p><br><br>
                    <form id="donate_button" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="9TA7SKMGJFCZ8">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="donate" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </div>

                <div id="results_header">
                    <a target="_blank" id="results_cover_big"><img width="133" height="133" id="results_cover"></a>
                    <div id="results_options">
                        <label><input id="tag_num" class="option" type="checkbox">Tag Track #</label><br>
                        <label><input id="tag_title" class="option" type="checkbox">Tag Title</label><br>
                        <label><input id="tag_artist" class="option" type="checkbox">Tag Artist</label><br>
                        <label><input id="tag_album" class="option" type="checkbox">Tag Album</label><br>
                        <label><input id="tag_img" class="option" type="checkbox">Tag Artwork</label><br>
                    </div>
                </div>

                <div id="main_results">
                    <div id="kitty">
                        <img id="kitty_img">
                    </div>
                    <table id="results_table"></table>
                </div>

                <div id="spinner"></div>
            </main>

            <div class="ad_box">
                <h4><?php echo $_SERVER['REMOTE_ADDR']; ?>, blocking ads is a good way to get DDOS attacked by my massive botnet...</h4>
                <h4>... just kidding, but please consider <a href="#donate">donating</a>.</h4>
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle"
                     style="display:block;width:728px;height:90px;position:absolute;top:10px;left:10px"
                     data-ad-client="ca-pub-4166408543945956"
                     data-ad-slot="1162321024"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>

            <footer id="footer">
                <div id="social">
                    <div id="s1"><a href="https://twitter.com/thirdletterdev">T</a></div>
                    <div id="s2"><a href="https://plus.google.com/+Thirdletter">G+</a></div>
                    <div id="s3"><a href="https://www.facebook.com/omgcatzwebsite">F</a></div>
                    <div id="s4"><a href="https://github.com/cadejscroggins/omgcatz">Git</a></div>
                </div>
                <div id="info">
                    <div><a href="tos">Terms</a></div>
                    <div><a href="#about">About</a></div>
                    <div><a href="mailto:admin@omgcatz.com">Contact</a></div>
                </div>
            </footer>
        </div>

        <iframe id="download_iframe" style="display:none"></iframe>
        <script src="js/main.js" type="text/javascript"></script>
    </body>
</html>
