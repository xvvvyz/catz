<?php
include '../include/functions.php';
include '../include/database.php';

function nextSong(&$playToken, $mixId, $trackNumber, $con) {
    $authToken = "3557239;13ede75e207a2348e6482b3bb4da509096e3d3e9";
    $retries = 0;

    do {
        $ch = curl_init("http://8tracks.com/sets/".$playToken."/next?mix_id=".$mixId."&format=jsonh&api_version=2");
        curl_setopt($ch, CURLOPT_COOKIE, "auth_token=".$authToken);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $jsonSongArray = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $status = $jsonSongArray['status'];
        $retries++;

        if ($retries > 1) {
            if (preg_match('/(403)/', $status)) {
                bail_out(403, "8tracks denied our request.");
            } else {
                bail_out(1, "8tracks denied our request. (Error: ".$status.")");
            } 
        }
    } while (!preg_match('/(200)/', $status));

    $songId = $jsonSongArray['set']['track']['id'];
    $title = addslashes($jsonSongArray['set']['track']['name']);
    $artist = addslashes($jsonSongArray['set']['track']['performer']);
    $album = addslashes($jsonSongArray['set']['track']['release_name']);
    $duration = $jsonSongArray['set']['track']['play_duration'];
    $songUrl = $jsonSongArray['set']['track']['url'];

    // if 8tracks_songs table doesn't exist, create it and 8tracks_playlists_songs
    $result = mysqli_query($con, "SHOW TABLES LIKE '8tracks_songs'");
    if(mysqli_num_rows($result) == 0) {
        $query = "CREATE TABLE `8tracks_songs` (
                  `songId` tinyblob NOT NULL,
                  `title` tinyblob NOT NULL,
                  `artist` tinyblob,
                  `album` tinyblob,
                  `duration` int(11) NOT NULL,
                  `songUrl` varchar(2083) NOT NULL DEFAULT '',
                  PRIMARY KEY (`songId`(255))
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        mysqli_query($con, $query);

        $query = "CREATE TABLE `8tracks_playlists_songs` (
                  `mixId` tinyblob NOT NULL,
                  `songId` int(11) NOT NULL,
                  `trackNumber` tinyblob NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        mysqli_query($con, $query);
    }

    // if songId isn't in the table, add a new row
    $query = "SELECT songId FROM 8tracks_songs
              WHERE songId='$songId' 
              LIMIT 1";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) == 0) {
        $query = "INSERT INTO 8tracks_songs
                  (songId,title,artist,album,duration,songUrl)
                  VALUES ('$songId','$title','$artist','$album','$duration','$songUrl')";
        mysqli_query($con, $query); // or die(mysqli_error($con))
    }

    $query = "INSERT INTO 8tracks_playlists_songs
              (mixId,songId,trackNumber)
              VALUES ('$mixId','$songId','$trackNumber')";
    mysqli_query($con, $query);
}

function getOutputArray(&$output, $mixArray, $mixId, $trackNumber, $con) {
    $query = "SELECT songId FROM 8tracks_playlists_songs
              WHERE mixId='$mixId'
              AND trackNumber >= $trackNumber
              ORDER BY trackNumber";
    $songs = mysqli_query($con, $query);

    if (mysqli_num_rows($songs) > 0) {
        $rows = array();
        
        while ($r = mysqli_fetch_assoc($songs)) {
            $songId = $r["songId"];

            $query = "SELECT * FROM 8tracks_songs
                      WHERE songId='$songId'";
            $result = mysqli_query($con, $query);

            while ($rr = mysqli_fetch_assoc($result)) {
                $rows[] = $rr;
            }
        }

        if (!empty($mixArray)) {
            $output = array_merge($mixArray, $rows);
        } else {
            $output = $rows;
        }
    } else {
        return 1;
    }

    return 0;
}

// printf("Error: %s\n", mysqli_error($con));

ignore_user_abort(true);

$lastUpdate = time();
$trackNumber = (isset($_POST["trackNumber"]) ? $_POST["trackNumber"] : 0);
$url = (isset($_POST["url"]) ? $_POST["url"] : "http://8tracks.com/mollysmiles/a-different-kind-of-trance");
$mixId = (isset($_POST["mixId"]) ? $_POST["mixId"] : "");
$playToken = (isset($_POST["playToken"]) ? $_POST["playToken"] : "");

// if no mixId then fetch the playlist info
if (empty($mixId)) {
    $ch = curl_init($url.".jsonp?api_key=3b7b9c79a600f667fe2113ff91183149779a74b8&api_version=3");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $mixArray = json_decode(curl_exec($ch), true);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode != 200) {
        bail_out(2, '8tracks returned '.$httpCode.'.');
    }

    $mixId = $mixArray['mix']['id'];
    $tracksCount = $mixArray['mix']['tracks_count'];
} else {
    $mixArray = "";
}

// if 8tracks_playlists table doesn't exist, create it
$result = mysqli_query($con, "SHOW TABLES LIKE '8track_playlists'");
if(mysqli_num_rows($result) == 0) {
    $query = "CREATE TABLE `8tracks_playlists` (
              `mixId` tinyblob NOT NULL,
              `tracksCount` int(11) NOT NULL,
              `playToken` int(11) DEFAULT NULL,
              `lastUpdate` int(11) DEFAULT NULL,
              PRIMARY KEY (`mixId`(255))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    mysqli_query($con, $query);
}

// select mix from table, if it exists
$query = "SELECT mixId FROM 8tracks_playlists
          WHERE mixId='$mixId' 
          LIMIT 1";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) < 1) {
    // if mixId isn't in the table, put it there

    $playToken = rand();

    $query = "INSERT INTO 8tracks_playlists
              (mixId,tracksCount,playToken,lastUpdate)
              VALUES ('$mixId','$tracksCount','$playToken','$lastUpdate')";
    mysqli_query($con, $query);

    nextSong($playToken, $mixId, $trackNumber, $con);
} else if (empty($playToken)) {
    /* if mix has already been entered and this
       is the clients first time requesting */

    $query = "SELECT tracksCount,playToken,lastUpdate FROM 8tracks_playlists
              WHERE mixId='$mixId' 
              LIMIT 1";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);

    $tracksCount = $row["tracksCount"];

    /* for when things start to change
    if ($oldTracksCount < $tracksCount) {
        $diff = $tracksCount - $oldTracksCount;
        $s = ($diff == 1 ? ' was' : 's were');
    }
    */
    
    $playToken = $row["playToken"];
    $lastUpdate = $row["lastUpdate"];
} else if (getOutputArray($output, $mixArray, $mixId, $trackNumber, $con)) {
    /* if there is nothing new in the database,
       fetch another song... */

    nextSong($playToken, $mixId, $trackNumber, $con);
}

getOutputArray($output, $mixArray, $mixId, $trackNumber, $con);

// release memory or whatever
mysqli_close($con);

// add our play token and error status to json
$add = array("play_token" => $playToken, "error" => 0);
$output = json_encode(array_merge($output, $add));
echo $output;
