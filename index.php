
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="initial-scale=0.8, width=device-width, user-scalable=yes">
        
        <title>Catz</title>

        <link rel="icon" type="image/png" href="favicon.ico">
        <link rel="stylesheet" href="css/main.css">
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
                <!--<div class="ad_box">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!\-\- Leader \-\->
                    <ins class="adsbygoogle"
                         style="display:inline-block;width:728px;height:90px"
                         data-ad-client="ca-pub-4166408543945956"
                         data-ad-slot="1162321024"></ins>
                    <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>-->

                <form id="main_form" method="get" name="main_form">
                    <?php echo '<input id="main_text" type="text" value="'.$_GET["q"].'">'."\n"; ?>
                    <input id="main_button" type="submit" value="=^.^=">
                </form>

                <div id="message"></div>

                <div id="hidden">
                    <!-- ALL -->
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

                    <!-- SONGZA -->
                    <span id="session_id"><?php echo substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 18); ?></span>
                    <span id="station_id"></span>
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
                        <img id="kitty_img" src="">
                    </div>
                    <table id="results_table"></table>
                </div>

                <div id="spinner"></div>
            </main>

            <footer id="footer">
            </footer>
        </div>

        <iframe id="download_iframe" style="display:none"></iframe>
        <script src="js/main.js"></script>
    </body>
</html>
