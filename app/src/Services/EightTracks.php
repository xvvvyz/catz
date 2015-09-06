<?php

namespace Omgcatz\Services;

use Omgcatz\Includes\Curl;
use Omgcatz\Includes\Database;
use Omgcatz\Services\Exceptions\ServiceException;

class EightTracks
{
  private $mixId;
  private $playToken;
  private $totalTracks;
  private $trackNumber;

  /**
   * @var array
   */
  private $data;


  /**
   * @var Database
   */
  private $db;

  /**
   * @var Curl
   */
  private $curl;

  public function __construct(Database $db, Curl $curl)
  {
    $this->db = $db;
    $this->curl = $curl;
    $this->data = [];
  }

  private function removeMixInfoDb()
  {
    $this->db->simpleQuery("delete from `8tracks_playlists` where `mixId`={$this->mixId}");
    $this->db->simpleQuery("delete from `8tracks_playlists_songs` where `mixId`={$this->mixId}");
  }

  private function updateMixInfoDb()
  {
    $this->playToken = 2147483647; // should be set dynamically ???

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
   * get mix info from url and put it into the database
   */
  private function updateMixInfo($url)
  {
    // get fresh mix info
    $response = $this->curl->getArray($url . "?Include=name&format=jsonh");

    if ($response["errors"]) {
      throw new ServiceException('8tracks said: ' . $response["errors"]);
    }

    $this->mixId = $response["mix"]["id"];
    $this->totalTracks = $response["mix"]["tracks_count"];

    if (empty($this->mixId)) {
      throw new ServiceException('Invalid URL: ' . $url);
    }

    // add info to $outputArray
    $this->data["mix"] = array(
      "id" => $this->mixId,
      "slug" => basename($response["mix"]["web_path"]),
      "name" => $response["mix"]["name"],
      "imgUrls" => array(
        "small" => $response["mix"]["cover_urls"]["sq133"],
        "medium" => $response["mix"]["cover_urls"]["sq500"],
        "original" => $response["mix"]["cover_urls"]["original"]
      ),
      "creator" => $response["mix"]["user"]["login"],
      "totalTracks" => $response["mix"]["tracks_count"],
      "duration" => $response["mix"]["duration"]
    );

    // get old info from database if it exists
    $mix = $this->db->select(
      "SELECT totalTracks FROM 8tracks_playlists WHERE mixId=? LIMIT 1",
      array($this->mixId),
      array("%d")
    );

    if (empty($mix)) {
      // if it doesn't exist, create a play token and add the new info

      $this->updateMixInfoDb();
    } else {
      // if it does, make sure nothing has changed

      if ($mix[0]["totalTracks"] != $this->totalTracks) {
        // if things have changed, wipe the playlist

        $this->removeMixInfoDb();
        $this->updateMixInfoDb();
      }
    }
  }

  /**
   * get existing songs from database
   */
  private function getSongsFromDb()
  {
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
          $this->data["songs"][] = $song;
        }
      }

      return 0;
    }

    return 1;
  }

  /**
   * get the next song in the playlist
   *
   * @throws ServiceException
   */
  private function nextSong()
  {
    $curl = new Curl();
    $songArray = $curl->getArray("http://8tracks.com/sets/" . $this->playToken . "/next?mix_id=" . $this->mixId . "&api_version=2&format=jsonh");

    $status = $songArray["status"];

    if (!preg_match('/(200)/', $status)) {
      if (preg_match('/(403)/', $status)) {
        $this->output->error("8tracks made a boo boo. (" . $status . ")", 403);
      } else {
        $this->output->error("8tracks made a boo boo. (" . $status . ")");
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
      throw new ServiceException('That\'s all we could find.');
    }
  }

  /**
   * get the playlist
   * @param string $url
   * @param string $mixId
   * @param int $trackNumber
   * @return array
   */
  function get($url, $mixId, $trackNumber)
  {
    ignore_user_abort(true);

    $this->mixId = $mixId;
    $this->trackNumber = $trackNumber;

    // if no $mixId then fetch $mixId and $totalTracks
    if (empty($mixId)) {
      $this->updateMixInfo($url);
    }

    $songs = $this->db->select(
      "SELECT mixId FROM 8tracks_playlists_songs WHERE mixId=? LIMIT 1",
      array($this->mixId),
      array("%d")
    );

    if (empty($songs)) {
      // if there aren't any songs in the database

      $this->nextSong();
      $this->getSongsFromDb();
    } else if ($this->getSongsFromDb()) {
      // if mix is in database and we need a new song

      $mix = $this->db->select(
        "SELECT playToken FROM 8tracks_playlists WHERE mixId=?",
        array($this->mixId),
        array("%d")
      );

      $this->playToken = $mix[0]["playToken"];
      $this->nextSong();
      $this->getSongsFromDb();
    }

    return $this->data;
  }

}
