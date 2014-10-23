<!doctype html>
<html>
<head>

  <!-- Website Settings -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, user-scalable=no">

  <!-- Title & Description -->
  <title>Catz</title>
  <meta name="description" content="If only there was something furry to enjoy while you listen to your jams. We have just what you need &#8211; free kittens.">

  <!-- Stylesheets -->
  <link rel="stylesheet" type="text/css" href="css/main.css">

  <!-- Open Graph Data -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="Catz">
  <meta property="og:description" content="If only there was something furry to enjoy while you listen to your jams. We have just what you need &#8211; free kittens.">
  <meta property="og:url" content="http://omgcatz.com">
  <meta property="og:image" content="http://omgcatz.com/img/omgcatz-share.jpg">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="Catz">
  <meta name="twitter:description" content="If only there was something furry to enjoy while you listen to your jams. We have just what you need &#8211; free kittens.">
  <meta name="twitter:url" content="http://omgcatz.com">
  <meta name="twitter:image" content="http://omgcatz.com/img/omgcatz-share.jpg">

  <!-- Fav Icon -->
  <link rel="icon" type="image/png" href="img/icons/favicon.ico">

  <!-- Apple Touch Icons -->
  <link rel="apple-touch-icon" href="img/icons/apple-touch-icon.png">
  <link rel="apple-touch-icon" sizes="57x57" href="img/icons/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="72x72" href="img/icons/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="114x114" href="img/icons/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="144x144" href="img/icons/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="57x57" href="img/icons/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="img/icons/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="114x114" href="img/icons/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="144x144" href="img/icons/apple-touch-icon-152x152.png">
  
  <!-- Windows 8 Tile Icons -->
  <meta name="msapplication-square70x70logo" content="img/icons/smalltile.png">
  <meta name="msapplication-square150x150logo" content="img/icons/mediumtile.png">
  <meta name="msapplication-wide310x150logo" content="img/icons/widetile.png">
  <meta name="msapplication-square310x310logo" content="img/icons/largetile.png">

<body>

  <div id="progress"><div class="bar"></div></div>

  <div id="money_header">
    <h4>You block ads, I block ads, we all block ads.<br><a class="pop_button" href="#donate">Click here to support omgcatz.</a></h4>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <ins class="adsbygoogle" style="display:block;width:728px;height:90px;position:absolute;top:0;left:0;background-color:#2B2B2B" data-ad-client="ca-pub-4166408543945956" data-ad-slot="1162321024"></ins>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
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

      <div class="popup" id="about_div">
        <a class="pop_button close_popup"><img src="img/x.png" alt="x"></a>
        <h2>OH FAQ!</h2>
        <p><b>What Is This?</b>This is a cat website (made for cats... like you). With the internet came an unruley amount of cats and cat like things. Maybe the Egyptians understood something we still don't quite understand. Along with cats, this website provides you with a whole bunch of music. Music to the eyes, that is, in the form of cats.</p>
        <p><b>Wait.. Why?</b>Because of <a href="http://www.youtube.com/v/xEhaVhta7sI">this</a>... And because obsessively viewing cat pictures while enjoying my favorite music can turn a bad day into something slightly better. I hope you can find joy from this website, too.</p>
        <p><b>How can I thank you?</b>You can drop me a line by <a href="mailto:admin@omgcatz.com">email</a>, and you can click <a class="pop_button" href="#donate">here</a> if you are interested in donating.</p>
      </div>

      <div class="popup" id="donate_div">
        <a class="pop_button close_popup"><img src="img/x.png" alt="x"></a>
        <h2>Help Me Help You</h2>
        
        <form name="paypal_form" class="donate_button" action="https://www.paypal.com/cgi-bin/webscr" method="post">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="9TA7SKMGJFCZ8">
          <p>This website gets traffic... a lot of it, and, due to special kittens, it requires a fairly strong (expensive) server to keep it from crashing. Thus, I display ads and accept donations. You can donate via <a href="https://coinbase.com/checkouts/fb89e8ada9a5ddab49c50b5d3f3854ad">Coinbase</a> or <a href="javascript:document.paypal_form.submit()">Paypal</a> if you're feeling supportive and can afford it.</p>
          <p>Thank you kindly.</p>
        </form>
      </div>

      <div class="popup" id="songza_issue_div">
        <a class="pop_button close_popup"><img src="img/x.png" alt="x"></a>
        <h2>Songza's Song Limit</h2>
        <p>Songza only allows a certain amount of unique, random songs to be played each time you listen to a playlist. After those songs, it forgets all of the songs that you have played and starts playing songs at random again. That means that, after a few songs, we won't be able to fetch all of the songs for downloading without waiting an exponential amount of time.</p>
        <p><b>No Comprehendo?</b>No worries, all it means for you is this: if the song you want to download doesn't show up the first time you load the playlist, you will have to fetch the playlist again and hope it shows up the next time.</p>
      </div>

      <section>
        <div id="results_header" class="hidden">
          <a target="_blank" id="results_cover_big"><img width="133" height="133" id="results_cover"></a>
          <div id="results_options">
            <label><input id="tag_num" class="option" type="checkbox">Tag Track #</label><br>
            <label><input id="tag_title" class="option" type="checkbox">Tag Title</label><br>
            <label><input id="tag_artist" class="option" type="checkbox">Tag Artist</label><br>
            <label><input id="tag_album" class="option" type="checkbox">Tag Album</label><br>
            <label><input id="tag_img" class="option" type="checkbox">Tag Artwork</label><br>
          </div>
        </div>
      </section>

      <div id="main_results">
        <div id="kitty">
          <img id="kitty_img">
        </div>
        <table id="results_table"></table>
      </div>

      <span id="loading">loading...</span>
    </main>

    <footer id="footer">
      <ul id="info">
        <li><a href="tos">Terms</a></li>
        <li><a href="mailto:admin@omgcatz.com">Contact</a></li>
        <li><a class="pop_button" href="#about">About</a></li>
      </ul>
      <ul id="social">
        <li><a href="//www.facebook.com/omgcatzwebsite">Facebook</a></li>
        <li><a href="//github.com/omgcatz/omgcatz">GitHub</a></li>
      </ul>
    </footer>
  </div>

  <iframe id="download_iframe" class="hidden"></iframe>
  <script src="js/main.js" type="text/javascript"></script>

  <div class="hidden">

    <!-- Global Data -->
    <span id="content_title"></span>
    <span id="timer"></span>
    <span id="domain"></span>
    <span id="url"></span>
    <span id="track_count"></span>
    <span id="total_tracks"></span>
    <span id="download_count"></span>
    <span id="song_duration">0</span>
    <span id="etr"></span>
    <span id="recursive_downloads">0</span>
    <span id="slug"></span>
    <span id="old_error"></span>

    <!-- 8tracks Data -->
    <span id="play_token"></span>
    <span id="mix_id"></span>
    <a id="results_cover_tag"></a>

    <!-- Songza Data -->
    <span id="station_id"></span>

  </div>

</body>
</html>
