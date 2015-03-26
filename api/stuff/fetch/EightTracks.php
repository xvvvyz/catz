<?php

include_once("include/Database.php");
include_once("include/Curl.php");

class EightTracks extends Database {

  // Mix info.
  private $url;
  private $mixId;
  private $playToken;
  private $totalTracks;
  private $trackNumber;

  // Output array.
  private $outputArray = array();

  /**
   * Get mix info from URL.
   */
  private function getMixInfo() {
    $curl = new Curl();
    $array = $curl->getArray($this->url.".jsonp?api_key=".$this->eightApiKey."&api_version=3");

    if ($array["errors"]) {
      $this->output->error("8tracks said: ".$errors);
    }

    $this->mixId = $array["mix"]["id"];
    $this->totalTracks = $array["mix"]["tracks_count"];

    if (empty($this->mixId)) {
      $this->output->error("Invalid URL: ".$this->url);
    }

    $this->outputArray["mix"] = array(
      "id"=>$this->mixId,
      "slug"=>basename($array["mix"]["web_path"]),
      "name"=>$array["mix"]["name"],
      "imgUrls"=>array(
        "small"=>$array["mix"]["cover_urls"]["sq133"],
        "medium"=>$array["mix"]["cover_urls"]["sq500"],
        "original"=>$array["mix"]["cover_urls"]["original"]
      ),
      "creator"=>$array["mix"]["user"]["login"],
      "totalTracks"=>$array["mix"]["tracks_count"],
      "duration"=>$array["mix"]["duration"]
    );
  }

  /**
   * Get existing songs from database.
   * @return boolean
   */
  private function getSongsFromDb() {
    $playlistSongs = $this->query("SELECT songId FROM 8tracks_playlists_songs WHERE mixId='".$this->mixId."' AND trackNumber >= ".$this->trackNumber." ORDER BY trackNumber");

    if (mysqli_num_rows($playlistSongs) > 0) {
      while ($playlistSong = mysqli_fetch_assoc($playlistSongs)) {
        $songs = $this->query("SELECT * FROM 8tracks_songs WHERE songId='".$playlistSong["songId"]."'");
        while ($song = mysqli_fetch_assoc($songs)) {
          $this->outputArray["songs"][] = $song;
        }
      }
      return 0;
    }

    return 1;
  }

  /**
   * Get the next song in the playlist.
   */
  private function nextSong() {
    $retries = 0;

    while (1) {
      $retries++;

      $curl = new Curl();
      $songArray = $curl->getArray("http://8tracks.com/sets/".$this->playToken."/next?format=jsonh&mix_id=".$this->mixId."&api_version=2");

      $status = $songArray["status"];

      if (preg_match('/(200)/', $status)) {
        break;
      } else if (preg_match('/(403)/', $status)) {
        $this->output->error("8tracks made a boo boo. (".$status.")", 403);
      } else if ($retries > 1) {
        $this->output->error("8tracks made a boo boo. (".$status.")");
      }
    }

    $songId = $songArray["set"]["track"]["id"];

    if (isset($songId)) {
      $title = addslashes($songArray["set"]["track"]["name"]);
      $artist = addslashes($songArray["set"]["track"]["performer"]);
      $album = addslashes($songArray["set"]["track"]["release_name"]);
      $duration = $songArray["set"]["track"]["play_duration"];
      $songUrl = $songArray["set"]["track"]["url"];

      // If $songId isn't in the table, add a new row.
      if (mysqli_num_rows($this->query("SELECT mixId FROM 8tracks_playlists_songs WHERE mixId='$this->mixId' AND songId='$songId' LIMIT 1")) == 0) {
        $this->query("INSERT INTO 8tracks_playlists_songs (mixId, songId, trackNumber) VALUES ('$this->mixId', '$songId', '$this->trackNumber')");

        if (mysqli_num_rows($this->query("SELECT songId FROM 8tracks_songs WHERE songId='$songId' LIMIT 1")) == 0) {
          $this->query("INSERT INTO 8tracks_songs (songId, title, artist, album, duration, songUrl) VALUES ('$songId', '$title', '$artist', '$album', '$duration', '$songUrl')");
        }
      }
    } else {
      $this->output->error("That's all we could find.");
    }
  }

  /**
   * Update mix info in DB and $outputArray.
   */
  private function updateMixInfo() {
    if (!$this->numRows("SELECT * FROM 8tracks_playlists WHERE mixId = $this->mixId LIMIT 1"))
      $this->query("INSERT INTO 8tracks_playlists (mixId, totalTracks, playToken) VALUES ('$this->mixId', '$this->totalTracks', '$this->playToken')");
  }

  /**
   * Get the playlist.
   * @param string $url
   * @param string $mixId
   * @param int $trackNumber
   */
  function get($url, $mixId, $trackNumber) {
    ignore_user_abort(true);

    $this->url = $url;
    $this->mixId = $mixId;
    $this->trackNumber = $trackNumber;

    // If no $mixId then fetch $mixId and $totalTracks.
    if (empty($mixId)) {
      $this->playToken = rand();
      $this->getMixInfo();
      $this->updateMixInfo();
    }

    if ($this->numRows("SELECT mixId FROM 8tracks_playlists_songs WHERE mixId=".$this->mixId." LIMIT 1") == 0) {
      // If there aren't any songs in the database.

      $this->nextSong();
      $this->getSongsFromDb();
    } else if ($this->getSongsFromDb()) {
      // If mix is in database and we need a new song.

      $row = $this->fetchRows("SELECT playToken FROM 8tracks_playlists WHERE mixId=".$this->mixId);
      $this->playToken = $row["playToken"];
      $this->nextSong();
      $this->getSongsFromDb();
    }

    $this->output->successWithData($this->outputArray);
  }
}
