/* countdown.js v2.5.2 http://countdownjs.org | Copyright (c)2006-2014 Stephen M. McKamey. | Licensed under The MIT License. */

var module,countdown=function(y){function C(a,b){var c=a.getTime();a.setMonth(a.getMonth()+b);return Math.round((a.getTime()-c)/864E5)}function z(a){var b=a.getTime(),c=new Date(b);c.setMonth(a.getMonth()+1);return Math.round((c.getTime()-b)/864E5)}function A(a,b){b=b instanceof Date||null!==b&&isFinite(b)?new Date(+b):new Date;if(!a)return b;var c=+a.value||0;if(c)return b.setTime(b.getTime()+c),b;(c=+a.milliseconds||0)&&b.setMilliseconds(b.getMilliseconds()+c);(c=+a.seconds||0)&&b.setSeconds(b.getSeconds()+
c);(c=+a.minutes||0)&&b.setMinutes(b.getMinutes()+c);(c=+a.hours||0)&&b.setHours(b.getHours()+c);(c=+a.weeks||0)&&(c*=7);(c+=+a.days||0)&&b.setDate(b.getDate()+c);(c=+a.months||0)&&b.setMonth(b.getMonth()+c);(c=+a.millennia||0)&&(c*=10);(c+=+a.centuries||0)&&(c*=10);(c+=+a.decades||0)&&(c*=10);(c+=+a.years||0)&&b.setFullYear(b.getFullYear()+c);return b}function l(a,b){return v(a)+(1===a?w[b]:x[b])}function q(){}function n(a,b,c,d,f,m){0<=a[c]&&(b+=a[c],delete a[c]);b/=f;if(1>=b+1)return 0;if(0<=a[d]){a[d]=
+(a[d]+b).toFixed(m);switch(d){case "seconds":if(60!==a.seconds||isNaN(a.minutes))break;a.minutes++;a.seconds=0;case "minutes":if(60!==a.minutes||isNaN(a.hours))break;a.hours++;a.minutes=0;case "hours":if(24!==a.hours||isNaN(a.days))break;a.days++;a.hours=0;case "days":if(7!==a.days||isNaN(a.weeks))break;a.weeks++;a.days=0;case "weeks":if(a.weeks!==z(a.refMonth)/7||isNaN(a.months))break;a.months++;a.weeks=0;case "months":if(12!==a.months||isNaN(a.years))break;a.years++;a.months=0;case "years":if(10!==
a.years||isNaN(a.decades))break;a.decades++;a.years=0;case "decades":if(10!==a.decades||isNaN(a.centuries))break;a.centuries++;a.decades=0;case "centuries":if(10!==a.centuries||isNaN(a.millennia))break;a.millennia++;a.centuries=0}return 0}return b}function D(a,b,c,d,f,m){var k=new Date;a.start=b=b||k;a.end=c=c||k;a.units=d;a.value=c.getTime()-b.getTime();0>a.value&&(k=c,c=b,b=k);a.refMonth=new Date(b.getFullYear(),b.getMonth(),15,12,0,0);try{a.millennia=0;a.centuries=0;a.decades=0;a.years=c.getFullYear()-
b.getFullYear();a.months=c.getMonth()-b.getMonth();a.weeks=0;a.days=c.getDate()-b.getDate();a.hours=c.getHours()-b.getHours();a.minutes=c.getMinutes()-b.getMinutes();a.seconds=c.getSeconds()-b.getSeconds();a.milliseconds=c.getMilliseconds()-b.getMilliseconds();var g;0>a.milliseconds?(g=s(-a.milliseconds/1E3),a.seconds-=g,a.milliseconds+=1E3*g):1E3<=a.milliseconds&&(a.seconds+=p(a.milliseconds/1E3),a.milliseconds%=1E3);0>a.seconds?(g=s(-a.seconds/60),a.minutes-=g,a.seconds+=60*g):60<=a.seconds&&(a.minutes+=
p(a.seconds/60),a.seconds%=60);0>a.minutes?(g=s(-a.minutes/60),a.hours-=g,a.minutes+=60*g):60<=a.minutes&&(a.hours+=p(a.minutes/60),a.minutes%=60);0>a.hours?(g=s(-a.hours/24),a.days-=g,a.hours+=24*g):24<=a.hours&&(a.days+=p(a.hours/24),a.hours%=24);for(;0>a.days;)a.months--,a.days+=C(a.refMonth,1);7<=a.days&&(a.weeks+=p(a.days/7),a.days%=7);0>a.months?(g=s(-a.months/12),a.years-=g,a.months+=12*g):12<=a.months&&(a.years+=p(a.months/12),a.months%=12);10<=a.years&&(a.decades+=p(a.years/10),a.years%=
10,10<=a.decades&&(a.centuries+=p(a.decades/10),a.decades%=10,10<=a.centuries&&(a.millennia+=p(a.centuries/10),a.centuries%=10)));b=0;!(d&1024)||b>=f?(a.centuries+=10*a.millennia,delete a.millennia):a.millennia&&b++;!(d&512)||b>=f?(a.decades+=10*a.centuries,delete a.centuries):a.centuries&&b++;!(d&256)||b>=f?(a.years+=10*a.decades,delete a.decades):a.decades&&b++;!(d&128)||b>=f?(a.months+=12*a.years,delete a.years):a.years&&b++;!(d&64)||b>=f?(a.months&&(a.days+=C(a.refMonth,a.months)),delete a.months,
7<=a.days&&(a.weeks+=p(a.days/7),a.days%=7)):a.months&&b++;!(d&32)||b>=f?(a.days+=7*a.weeks,delete a.weeks):a.weeks&&b++;!(d&16)||b>=f?(a.hours+=24*a.days,delete a.days):a.days&&b++;!(d&8)||b>=f?(a.minutes+=60*a.hours,delete a.hours):a.hours&&b++;!(d&4)||b>=f?(a.seconds+=60*a.minutes,delete a.minutes):a.minutes&&b++;!(d&2)||b>=f?(a.milliseconds+=1E3*a.seconds,delete a.seconds):a.seconds&&b++;if(!(d&1)||b>=f){var h=n(a,0,"milliseconds","seconds",1E3,m);if(h&&(h=n(a,h,"seconds","minutes",60,m))&&(h=
n(a,h,"minutes","hours",60,m))&&(h=n(a,h,"hours","days",24,m))&&(h=n(a,h,"days","weeks",7,m))&&(h=n(a,h,"weeks","months",z(a.refMonth)/7,m))){d=h;var e,l=a.refMonth,q=l.getTime(),r=new Date(q);r.setFullYear(l.getFullYear()+1);e=Math.round((r.getTime()-q)/864E5);if(h=n(a,d,"months","years",e/z(a.refMonth),m))if(h=n(a,h,"years","decades",10,m))if(h=n(a,h,"decades","centuries",10,m))if(h=n(a,h,"centuries","millennia",10,m))throw Error("Fractional unit overflow");}}}finally{delete a.refMonth}return a}
function e(a,b,c,d,f){var e;c=+c||222;d=0<d?d:NaN;f=0<f?20>f?Math.round(f):20:0;var k=null;"function"===typeof a?(e=a,a=null):a instanceof Date||(null!==a&&isFinite(a)?a=new Date(+a):("object"===typeof k&&(k=a),a=null));var g=null;"function"===typeof b?(e=b,b=null):b instanceof Date||(null!==b&&isFinite(b)?b=new Date(+b):("object"===typeof b&&(g=b),b=null));k&&(a=A(k,b));g&&(b=A(g,a));if(!a&&!b)return new q;if(!e)return D(new q,a,b,c,d,f);var k=c&1?1E3/30:c&2?1E3:c&4?6E4:c&8?36E5:c&16?864E5:6048E5,
h,g=function(){e(D(new q,a,b,c,d,f),h)};g();return h=setInterval(g,k)}var s=Math.ceil,p=Math.floor,w,x,r,t,u,v,B;q.prototype.toString=function(a){var b=B(this),c=b.length;if(!c)return a?""+a:u;if(1===c)return b[0];a=r+b.pop();return b.join(t)+a};q.prototype.toHTML=function(a,b){a=a||"span";var c=B(this),d=c.length;if(!d)return(b=b||u)?"\x3c"+a+"\x3e"+b+"\x3c/"+a+"\x3e":b;for(var f=0;f<d;f++)c[f]="\x3c"+a+"\x3e"+c[f]+"\x3c/"+a+"\x3e";if(1===d)return c[0];d=r+c.pop();return c.join(t)+d};q.prototype.addTo=
function(a){return A(this,a)};B=function(a){var b=[],c=a.millennia;c&&b.push(l(c,10));(c=a.centuries)&&b.push(l(c,9));(c=a.decades)&&b.push(l(c,8));(c=a.years)&&b.push(l(c,7));(c=a.months)&&b.push(l(c,6));(c=a.weeks)&&b.push(l(c,5));(c=a.days)&&b.push(l(c,4));(c=a.hours)&&b.push(l(c,3));(c=a.minutes)&&b.push(l(c,2));(c=a.seconds)&&b.push(l(c,1));(c=a.milliseconds)&&b.push(l(c,0));return b};e.MILLISECONDS=1;e.SECONDS=2;e.MINUTES=4;e.HOURS=8;e.DAYS=16;e.WEEKS=32;e.MONTHS=64;e.YEARS=128;e.DECADES=256;
e.CENTURIES=512;e.MILLENNIA=1024;e.DEFAULTS=222;e.ALL=2047;e.setLabels=function(a,b,c,d,f,e){a=a||[];a.split&&(a=a.split("|"));b=b||[];b.split&&(b=b.split("|"));for(var k=0;10>=k;k++)w[k]=a[k]||w[k],x[k]=b[k]||x[k];r="string"===typeof c?c:r;t="string"===typeof d?d:t;u="string"===typeof f?f:u;v="function"===typeof e?e:v};(e.resetLabels=function(){w=" millisecond; second; minute; hour; day; week; month; year; decade; century; millennium".split(";");x=" milliseconds; seconds; minutes; hours; days; weeks; months; years; decades; centuries; millennia".split(";");
r=" and ";t=", ";u="";v=function(a){return a}})();y&&y.exports?y.exports=e:"function"===typeof window.define&&"undefined"!==typeof window.define.amd&&window.define("countdown",[],function(){return e});return e}(module);

/* omgcatz */

function UserInterface() {

  this.disableElement = function(element) {
    $(element).attr("disabled", true);

    if ($(element).is(":checkbox")) {
      $(element).attr("checked", false);
    }
  };


  this.enableElement = function(element) {
    $(element).removeAttr("disabled");
  }

}


function ProgressBar() {

  this.init = function() {
    $("#progress").stop(true).show();
    $("#progress").animate({
      width: 0
    }, FAST);
    $("#progress").animate({
      opacity: 1
    }, 0);
  };


  this.animate = function(percentage, speed) {
    $("#progress").animate({
      width: percentage + "%"
    }, speed, "linear");
  };


  this.done = function() {
    $("#progress").stop(true).animate({
      width: "100%",
      opacity: 0.2
    }, NORMAL);
  }

}


function Message() {

  this.show = function(message) {
    $("#message").html(message);
    $("#message").slideDown();
  };


  this.hide = function() {
    $("#message").slideUp(FAST);
    $("#message").html("");
  };
}


function Modal() {

  this.show = function(element) {
    $("html, body").animate({ scrollTop: 100 }, "slow");
    $(".modal").slideUp(FASTER);
    $("#" + $(element).attr("data-modal")).slideDown(FAST);
  };


  this.hide = function() {
    $(".modal").slideUp(FASTER);
  };
}


function Checkboxes() {

  this.toggleAll = function() {
    if ($("#select-downloads").prop("checked")) {
      $(".selected-downloads").each(function () {
        if ($(this).is(":visible")) $(this).prop("checked", "checked");
      });
    } else {
      $(".selected-downloads").prop("checked", false);
    }
  };


  this.countChecked = function() {
    var count = 0;

    for (var i = 1; i <= FETCH.getTrackCount(); i++) {
      if ($("#selected-download-" + i).prop("checked")) {
        count++;
      }
    }

    return count;
  };
}

function Timer() {

  var timer;
  var timeout;
  var parentThis = this;


  this.addSeconds = function(seconds) {
    timer = new Date();
    timer = timer.setSeconds(timer.getSeconds() + seconds);
  };


  this.update = function() {
    if (timer != false) {
      prettyTime = countdown(timer).toString();

      if (timer <= new Date().getTime() || prettyTime == "") {
        MESSAGE.show("Fetching next song...");

        switch (FETCH.getDomain()) {
          case "8tracks.com":
            FETCH.eightTracks();
            break;
          case "songza.com":
            FETCH.songza();
        }
      } else {
        var progressWidth = parseFloat($("#progress").width());
        var progressBarWidth = parseFloat($("#progress-bar").width());
        var percentage = progressWidth / progressBarWidth * 100;

        document.title = "loading... " + percentage.toFixed(2) + "%";
        MESSAGE.show("Fetching next song in " + prettyTime + ".");

        timeout = setTimeout(parentThis.update, 1000);
      }
    }
  };

  this.clear = function() {
    timer = false;
    clearTimeout(timeout);
    MESSAGE.hide();
  };
}

function Fetch() {

  var url;
  var domain;
  var mixSlug;
  var mixId;
  var mixArtwork;
  var trackCount;
  var totalTracks;

  // eightTracks
  var mixName;
  var previousSongDuration;
  var previousError;

  // songza
  var sessionId;


  this.getDomain = function() { return domain; };
  this.getMixSlug = function() { return mixSlug; };
  this.getMixId = function() { return mixId; };
  this.getMixArtwork = function() { return mixArtwork; };
  this.getTrackCount = function() { return trackCount; };
  this.getTotalTracks = function() { return totalTracks; };


  this.init = function() {
    document.title = "loading...";

    mixId = "";
    trackCount = 0;

    $("#loading").slideDown(FAST);
    $("#kitty").slideUp(FAST);
    $("#results-header").slideUp(NORMAL);
    $("#results-table").fadeOut(FASTER);

    PROGRESS_BAR.init();
    TIMER.clear();
    MINION.init();
    UI.enableElement(".option");
  };


  this.done = function() {
    document.title = "Catz";

    $("#loading").slideUp(FAST);

    PROGRESS_BAR.done();
    TIMER.clear();
  };

  this.cat = function() {
    PROGRESS_BAR.animate(95, 12000);

    var img = $("#kitty-img").attr("src", "http://thecatapi.com/api/images/get#"+new Date().getTime()).load(function () {
      FETCH.done();
      $("#kitty").fadeIn(SLOWER);
    }).error(function () {
      FETCH.done();
      MESSAGE.show("Nothing was found.");
    });
  };

  this.eightTracks = function() {
    UI.disableElement("#tag-genre");

    $.ajax({
      type: "POST",
      url: "/fetch",
      dataType: "json",
      data: {
        what: "fetch",
        url: url,
        mix_id: mixId,
        track_number: trackCount
      },
      success: function(data) {
        var error = data["error"];

        if (error == 0) {
          if (data["mix"]) {
            mixId = data["mix"]["id"];
            mixName = data["mix"]["name"];
            totalTracks = data["mix"]["totalTracks"];
            mixArtwork = data["mix"]["imgUrls"]["original"];
            mixSlug = data["mix"]["slug"];

            RESULTS.init(EIGHT_TRACKS_RESULTS_HEADER, mixArtwork);
          }

          if (typeof data["songs"] !== "undefined") {
            for (var i = 0; typeof data["songs"][i] !== "undefined"; i++) {
              trackCount++;
              var songDuration = parseInt(data["songs"][i]["duration"]);

              RESULTS.append('<tr class="songs row-' + trackCount + '" id="row-' + trackCount + '"><td class="right">' + trackCount + '</td><td id="song-title-' + trackCount + '" class="left">' + data["songs"][i]["title"] + '</td><td id="song-artist-' + trackCount + '" class="left song-artists">' + data["songs"][i]["artist"] + '</td><td id="song-album-' + trackCount + '" class="left song-albums">' + mixName + '</td><td><a id="song-url-' + trackCount + '" href="' + data["songs"][i]["songUrl"] + '"></a><a id="song-id-' + trackCount + '" href="' + data["songs"][i]["songId"] + '"></a><input id="download-submit-' + trackCount + '" class="download-buttons" type="button" onclick="MINION.download(' + trackCount + ');" value="Download"><span id="status' + trackCount + '"></span></td><td><input type="checkbox" class="selected-downloads" id="selected-download-' + trackCount + '"><div id="down-loader-' + trackCount + '" class="down-loaders"><img width="20" height="20" src="/img/download.gif" alt=""/></div><span id="completed-' + trackCount + '" class="completed"></span></td></tr>');
            }

            if (trackCount >= totalTracks) {
              FETCH.done();
              TIMER.update();
            } else {
              var percentage = Math.floor(trackCount / totalTracks * 100);

              PROGRESS_BAR.animate(percentage, SLOW);
              FETCH.eightTracks();
            }
          } else {
            FETCH.done();
            MESSAGE.show("That's all we could find.");
          }
        } else if (error == 403) {
          if (previousError != 403) {
            var updateTime = previousSongDuration / 2 + EIGHT_TRACKS_FETCH_PADDING;
            var speed = updateTime * 1000;
            var percentage = Math.floor((trackCount + 1) / totalTracks * 100);

            TIMER.addSeconds(updateTime);
            TIMER.update();
            PROGRESS_BAR.animate(percentage, speed);
          } else {
            FETCH.done();
          }
        } else {
          FETCH.done();
          MESSAGE.show(data.status);
        }

        previousError = error;
        previousSongDuration = songDuration;
      },
      error: function (jqXHR, textStatus) {
        FETCH.done();
        MESSAGE.show("Request failed. (" + $.parseJSON(jqXHR.responseText).error + ")");
      }
    });
  };


  this.songza = function() {
    UI.disableElement("#tag-num");

    $.ajax({
      type: "POST",
      url: "/stuff",
      dataType: "json",
      data: {
        url: url,
        session_id: sessionId,
        station_id: mixId
      },
      success: function (data) {
        var error = data["error"];

        if (error == 0) {
          if (data["mix"]) {
            sessionId = data["mix"]["sessionId"];
            mixId = data["mix"]["id"];
            totalTracks = data["mix"]["totalTracks"];

            RESULTS.init(SONGZA_RESULTS_HEADER, data["mix"]["imgUrls"]["large"]);
          }

          if (typeof data["song"] !== "undefined") {
            trackCount++;

            var smallCoverUrl = data["song"]["coverUrls"]["small"];
            var bigCoverUrl = data["song"]["coverUrls"]["large"];

            RESULTS.append('<tr class="songs row-' + trackCount + '" id="row-' + trackCount + '"><td class="song-img-url"><a id="song-artwork-' + trackCount + '" target="_blank" href="' + bigCoverUrl + '"><img width="75" height="75" src="' + smallCoverUrl + '"></a></td><td id="song-title-' + trackCount + '" class="left">' + data["song"]["title"] + '</td><td id="song-artist-' + trackCount + '" class="left song-artists">' + data["song"]["artist"] + '</td><td id="song-album-' + trackCount + '" class="left song-albums">' + data["song"]["album"] + '</td><td><a id="song-url-' + trackCount + '" href="' + data["song"]["url"] + '"></a><a id="song-id-' + trackCount + '" href="' + data["song"]["id"] + '"></a><input id="download-submit-' + trackCount + '" class="download-buttons" type="button" onclick="MINION.download(' + trackCount + ');" value="Download"><span id="status-' + trackCount + '"></span></td><td><input type="checkbox" class="selected-downloads" id="selected-download-' + trackCount + '"><div id="down-loader-' + trackCount + '" class="down-loaders"><img width="20" height="20" src="/img/download.gif" alt=""/></div><span id="completed-' + trackCount + '" class="completed"></span></td></tr>');

            var percentage = Math.floor((trackCount + 1) / totalTracks * 100);
            var speed = SONGZA_FETCH_PADDING * 1000;

            TIMER.addSeconds(SONGZA_FETCH_PADDING);
            TIMER.update();

            if (trackCount < totalTracks) {
              PROGRESS_BAR.animate(percentage, speed);
            } else {
              FETCH.done();
              MESSAGE.show("That's all we could find.");
            }
          } else {
            FETCH.done();
            MESSAGE.show('Is a song that you wanted missing? Click <a class="md-trigger" data-modal="songza-issue">here</a> for more info.');
          }
        }
      },
      error: function (jqXHR, textStatus) {
        MESSAGE.show("Request failed. (" + textStatus + ")");
        FETCH.done();
      }
    });
  };


  this.soundCloud = function() {
    FETCH.done();
  };


  this.stuff = function(original) {
    FETCH.init();

    url = original.split("#")[0];
    url = url.split("?")[0];
    url = url.replace(/\s+/g, ' ');
    url = url.replace("http://m.", "http://");
    url = url.replace("https://", "http://");

    window.location.hash = url;

    domain = url.split("/")[2];

    switch (domain) {
      case "8tracks.com":
        FETCH.eightTracks();
        break;
      default:
        FETCH.cat(original);
    }
  }

}


function Minion() {

  var downloadCount;
  var recursiveDownloadCount;
  var server;


  this.init = function() {
    downloadCount = 0;
    recursiveDownloadCount = 0;
    server = "";
  };


  this.downloadId = function() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 10; i++) {
      text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text;
  };


  this.download = function(position, recursive, downloadId) {
    recursive = typeof recursive !== "undefined" ? recursive : null;
    downloadId = typeof downloadId !== "undefined" ? downloadId : MINION.downloadId();

    var mixId = FETCH.getMixId();
    var mixSlug = FETCH.getMixSlug();
    var totalTracks = FETCH.getTotalTracks();

    if (recursive) {
      for (position = 1; position <= totalTracks; position++) {
        var checked = $("#selected-download-" + position).prop("checked");
        if (checked) break;
      }

      if (!checked) {
        MINION.archive(downloadId, mixSlug);

        recursiveDownloadCount = 0;
        $(".download-buttons").removeAttr("disabled");

        return false;
      }
    }

    var songId = $("#song-id-" + position).attr("href");
    var tagTitle = $("#tag-title").prop("checked") ? 1 : null;
    var songTitle = $("#song-title-" + position).html();
    var songArtist = $("#tag-artist").prop("checked") ? $("#song-artist-" + position).html() : null;
    var songAlbum = $("#tag-album").prop("checked") ? $("#song-album-" + position).html() : null;
    var songUrl = $("#song-url-" + position).attr("href");
    var songNumber = $("#tag-num").prop("checked") ? position : null;

    $(".download-buttons").attr("disabled", "disabled");
    $("#selected-download-" + position).hide();
    $("#selected-download-" + position).prop("checked", false);
    $("#down-loader-" + position).show();

    if ($("#tag-img").prop("checked")) {
      var songArtwork = $("#song-artwork-" + position).attr("href");
      if (!songArtwork) {
        var mixArtwork = FETCH.getMixArtwork();
      }
    } else {
      var songArtwork = null, mixArtwork = null;
    }

    $.ajax({
      type: "POST",
      url: "/download",
      dataType: "json",
      data: {
        server: server,
        what: "download",
        song_id: songId,
        tag_song_title: tagTitle,
        song_title: songTitle,
        song_artist: songArtist,
        song_album: songAlbum,
        song_artwork: songArtwork,
        song_genre: null,
        song_url: songUrl,
        song_number: songNumber,
        total_songs: totalTracks,
        mix_id: mixId,
        mix_artwork: mixArtwork,
        mix_slug: mixSlug,
        itunes_compilation: null,
        recursive: recursive,
        download_id: downloadId
      },
      success: function (data) {
        if (data["error"] == 0) {
          $("#status" + position).html("ok");
          $("#download-submit-" + position).hide();

          server = data["server"];

          if (recursive) {
            recursiveDownloadCount++;

            $("#completed-" + position).html("cached");

            MINION.download(position, recursive, downloadId);
          } else {
            downloadCount++;

            MINION.clientDownload(server, data["path"], data["save"]);

            $(".download-buttons").removeAttr("disabled");
            $("#main-button").removeAttr("disabled");
            $("#completed-" + position).html("complete");
          }
        } else {
          $("#status-" + position).html("failed");
        }

        $("#completed" + position).show();
        $("#status" + position).show();
      },
      error: function(jqXHR, textStatus) {
        MESSAGE.show("Request failed. (" + textStatus + ")");
        $(".download-buttons").removeAttr("disabled");
        $("#main-button").removeAttr("disabled");
        $("#selected-download-" + position).show();
      },
      complete: function() {
        $("#down-loader-" + position).hide();
      }
    });

    return false;
  };

  this.archive = function(downloadId, mixSlug) {
    $(".completed").each(function() {
      if ($(this).html() == "cached") {
        $(this).html("preparing...");
      }
    });

    $.ajax({
      type: "POST",
      url: "/archive",
      data: {
        server: server,
        what: "archive",
        download_id: downloadId,
        slug: mixSlug
      },
      success: function(data) {
        $(".completed").each(function() {
          if ($(this).html() == "preparing...") {
            $(this).html("completed");
          }
        });

        var path = "download/archives" + "/" + mixSlug + "/" + downloadId + "/" + mixSlug + ".zip";
        var fileName = mixSlug + ".zip";

        MINION.clientDownload(server, path, fileName);
      },
      error: function(jqXHR, textStatus) {
        $(".completed").each(function() {
          if ($(this).html() == "preparing...") {
            $(this).html("archive failed");
          }
        });
      }
    });

    return false;
  };

  this.clientDownload = function(server, path, fileName) {
    var downloadUrl = server + "magic?p=" + path + "&s=" + encodeURIComponent(fileName);
    $("#download").attr("src", downloadUrl);
  }

}


function Results() {

  this.init = function(header, heroImg) {
    $("#results-table").html(header);
    $("#results-header").css("background-image", "url(" + heroImg + ")");
  };


  this.append = function(row) {
    $("#results-header").slideDown(FAST);
    $("#results-table").show();
    $("#results-table").append(row);
    $("#results-table tr:last").hide();
    $("#results-table tr:last").fadeIn(FAST);
  };

}


const FASTER = 100;
const FAST   = 200;
const NORMAL = 300;
const SLOW   = 400;
const SLOWER = 500;

const EIGHT_TRACKS_RESULTS_HEADER = '<tr id="table-title-row"><th></th><th class="left">Title</th><th class="left">Artist</th><th class="left">Album</th><th><input class="download-buttons" type="button" onclick="MINION.download(1, 1);" value="Download Selected"></th><th><input type="checkbox" class="selected-downloads" id="select-downloads" onclick="CHECKBOXES.toggleAll();"></th></tr>';
const SONGZA_RESULTS_HEADER = EIGHT_TRACKS_RESULTS_HEADER;

const EIGHT_TRACKS_FETCH_PADDING = 7;
const SONGZA_FETCH_PADDING = 3;

const UI           = new UserInterface();
const PROGRESS_BAR = new ProgressBar();
const MESSAGE      = new Message();
const MODAL        = new Modal();
const CHECKBOXES   = new Checkboxes();
const TIMER        = new Timer();
const FETCH        = new Fetch();
const MINION       = new Minion();
const RESULTS      = new Results();


$(function() {

  $("#main-form").submit(function(e) {
    e.preventDefault();
    FETCH.stuff($("#main-text").val());
  });


  $(".md-trigger").click(function() {
    MODAL.show(this);
  });


  $(".md-close").click(function() {
    MODAL.hide();
  });

});


$(document).ready(function() {

  var hash = location.hash.substring(1);

  if (hash) {
    $("#main-text").val(hash);
    FETCH.stuff(hash);
  }

});
