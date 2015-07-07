<?php

require_once "include/Database.php";
require_once "include/Curl.php";

class EightTracks {

  // Mix info
  private $mixId;
  private $playToken;
  private $totalTracks;
  private $trackNumber;

  // Output array
  private $outputArray = array();

  // Output object
  private $output;

  // Database object
  private $db;

  /**
   * Constructor
   * @param object $output
   */
  public function __construct($output) {
    $this->output = $output;
    $this->db = new Database();
  }

  private function getPlayToken() {
    $curl = new Curl();
    $response = $curl->getArray("http://8tracks.com/sets/new.json?api_key=".Config::$eightTracksApiKey."&api_version=3");
    return $response["play_token"];
  }

  private function removeMixInfoDb() {
    $this->db->simpleQuery("delete from `8tracks_playlists` where `mixId`={$this->mixId}");
    $this->db->simpleQuery("delete from `8tracks_playlists_songs` where `mixId`={$this->mixId}");
  }

  private function updateMixInfoDb() {
      $this->playToken = $this->getPlayToken();

      $this->db->insert(
        "8tracks_playlists",
        array(
          "mixId" => $this->mixId,
          "totalTracks" => $this->totalTracks,
          "playToken" => $this->playToken
        ),
        array("%d", "%d", "%s")
      );
  }

  /**
   * Get mix info from URL and put it into the database
   */
  private function updateMixInfo($url) {
    $curl = new Curl();

    // Get fresh mix info
    $response = $curl->getArray($url.".jsonp?api_key=".Config::$eightTracksApiKey."&api_version=3");

    if ($response["errors"]) {
      $this->output->error("8tracks said: ".$errors);
    }

    $this->mixId = $response["mix"]["id"];
    $this->totalTracks = $response["mix"]["tracks_count"];

    if (empty($this->mixId)) {
      $this->output->error("Invalid URL: ".$url);
    }

    // Add info to $outputArray
    $this->outputArray["mix"] = array(
      "id"=>$this->mixId,
      "slug"=>basename($response["mix"]["web_path"]),
      "name"=>$response["mix"]["name"],
      "imgUrls"=>array(
        "small"=>$response["mix"]["cover_urls"]["sq133"],
        "medium"=>$response["mix"]["cover_urls"]["sq500"],
        "original"=>$response["mix"]["cover_urls"]["original"]
      ),
      "creator"=>$response["mix"]["user"]["login"],
      "totalTracks"=>$response["mix"]["tracks_count"],
      "duration"=>$response["mix"]["duration"]
    );

    // Get old info from database if it exists
    $mix = $this->db->select(
      "SELECT totalTracks FROM 8tracks_playlists WHERE mixId=? LIMIT 1",
      array($this->mixId),
      array("%d")
    );

    if (empty($mix)) {
      // If it doesn't exist, create a play token and add the new info

      $this->updateMixInfoDb();
    } else {
      // If it does, make sure nothing has changed

      if ($mix[0]["totalTracks"] != $this->totalTracks) {
        // If things have changed, wipe the playlist

        $this->removeMixInfoDb();
        $this->updateMixInfoDb();
      }
    }
  }

  /**
   * Get existing songs from database
   * @return boolean
   */
  private function getSongsFromDb() {
    $playlistSongs = $this->db->select(
      "SELECT songId FROM 8tracks_playlists_songs WHERE mixId=? AND trackNumber>=? ORDER BY trackNumber",
      array($this->mixId, $this->trackNumber),
      array("%d", "%d")
    );

    if (!empty($playlistSongs)) {
      foreach ($playlistSongs as $playlistSong) {
        $songs = $this->db->select(
          "SELECT * FROM 8tracks_songs WHERE songId=?",
          array($playlistSong["songId"]),
          array("%d")
        );

        foreach ($songs as $song) {
          $this->outputArray["songs"][] = $song;
        }
      }

      return 0;
    }

    return 1;
  }

  /**
   * Get the next song in the playlist
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

    if (isset($songArray["set"]["track"]["id"])) {
      $songId = $songArray["set"]["track"]["id"];
      $title = $songArray["set"]["track"]["name"];
      $artist = $songArray["set"]["track"]["performer"];
      $album = $songArray["set"]["track"]["release_name"];
      $duration = $songArray["set"]["track"]["play_duration"];
      $songUrl = $songArray["set"]["track"]["url"];

      $song = $this->db->select(
        "SELECT mixId FROM 8tracks_playlists_songs WHERE mixId=? AND songId=? LIMIT 1",
        array($this->mixId, $songId),
        array("%d", "%d")
      );

      if (empty($song)) {
        $this->db->insert(
          "8tracks_playlists_songs",
          array(
            "mixId" => $this->mixId,
            "songId" => $songId,
            "trackNumber" => $this->trackNumber
          ),
          array("%d", "%d", "%d")
        );

        $song = $this->db->select(
          "SELECT songId FROM 8tracks_songs WHERE songId=? LIMIT 1",
          array($songId),
          array("%d")
        );

        if (empty($song)) {
          $this->db->insert(
            "8tracks_songs",
            array(
              "songId" => $songId,
              "title" => $title,
              "artist" => $artist,
              "album" => $album,
              "duration" => $duration,
              "songUrl" => $songUrl
            ),
            array("%d", "%s", "%s", "%s", "%d", "%s")
          );
        }
      }
    } else {
      $this->output->error("That's all we could find.");
    }
  }

  /**
   * Get the playlist
   * @param string $url
   * @param string $mixId
   * @param int $trackNumber
   */
  function get($url, $mixId, $trackNumber) {
    ignore_user_abort(true);

    $this->mixId = $mixId;
    $this->trackNumber = $trackNumber;

    // If no $mixId then fetch $mixId and $totalTracks
    if (empty($mixId)) {
      $this->updateMixInfo($url);
    }

    $songs = $this->db->select(
      "SELECT mixId FROM 8tracks_playlists_songs WHERE mixId=? LIMIT 1",
      array($this->mixId),
      array("%d")
    );

    if (empty($songs)) {
      // If there aren't any songs in the database

      $this->nextSong();
      $this->getSongsFromDb();
    } else if ($this->getSongsFromDb()) {
      // If mix is in database and we need a new song

      $mix = $this->db->select(
        "SELECT playToken FROM 8tracks_playlists WHERE mixId=?",
        array($this->mixId),
        array("%d")
      );

      $this->playToken = $mix[0]["playToken"];
      $this->nextSong();
      $this->getSongsFromDb();
    }

    $this->output->successWithData($this->outputArray);
  }

}
