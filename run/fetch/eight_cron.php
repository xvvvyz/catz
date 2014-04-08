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

$updateTime = time();

// select mixes from table
$query = "SELECT mixId FROM 8tracks_playlists
          WHERE lastUpdate<$updateTime";
$result = mysqli_query($con, $query);


    $query = "SELECT tracksCount,playToken,lastUpdate FROM 8tracks_playlists
              WHERE mixId='$mixId' 
              LIMIT 1";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);

    $tracksCount = $row["tracksCount"];
    
    $playToken = $row["playToken"];
    $lastUpdate = $row["lastUpdate"];

    nextSong($playToken, $mixId, $trackNumber, $con);


// release memory or whatever
mysqli_close($con);
