<?php

namespace Omgcatz\Services;

use Omgcatz\Includes\Curl;
use Omgcatz\Includes\Output\Output;

class Songza
{

  private $url;
  private $stationId;
  private $sessionId;

  private $outputArray = array();

  /**
   * @var Output
   */
  private $output;

  /**
   * @var Curl
   */
  private $curl;

  public function __construct(Curl $curl, Output $output)
  {
    $this->curl = $curl;
    $this->output = $output;
  }

  /**
   * get mix info from url
   */
  private function getMixInfo()
  {
    $source = $this->curl->get($this->url);

    // grab station id
    preg_match_all('/data-sz-station-id="[^"]*/', $source, $matches);
    list(, $this->stationId) = explode('"', $matches[0][0]);

    // check for no station id
    if (empty($this->stationId)) {
      $this->output->error("Invalid URL: " . $this->url);
    }

    // grab station info
    $array = $this->curl->getArray("http://songza.com/src/1/station/" . $this->stationId);

    // check for failed info grab
    if ($array["status"] != "NORMAL") {
      $this->output->error("Songza said: " . $array["status"]);
    }

    // put info into array
    $array = array(
      "id" => $this->stationId,
      "sessionId" => $this->sessionId,
      "slug" => $array["dasherized_name"],
      "name" => $array["name"],
      "imgUrls" => array(
        "small" => "http://songza.com/src/1/station/" . $this->stationId . "/image?size=133",
        "medium" => "http://songza.com/src/1/station/" . $this->stationId . "/image?size=500",
        "large" => "http://songza.com/src/1/station/" . $this->stationId . "/image?size=1000"
      ),
      "creator" => $array["creator_name"],
      "totalTracks" => $array["song_count"]
    );

    // add to output array
    $this->outputArray["mix"] = $array;
  }

  /**
   * get song info from url
   */
  private function nextSong()
  {
    // grab song info
    $curl = new Curl();
    $array = $curl->getArray("http://songza.com/src/1/station/" . $this->stationId . "/next", "sessionid=" . $this->sessionId . "; visitor-prompted:1");

    // clean up array
    $array = array(
      "id" => $array["song"]["id"],
      "title" => $array["song"]["title"],
      "artist" => $array["song"]["artist"]["name"],
      "album" => $array["song"]["album"],
      "genre" => $array["song"]["genre"],
      "url" => $array["listen_url"],
      "coverUrls" => array(
        "small" => $array["song"]["cover_url"],
        "medium" => $array["song"]["cover_url"],
        "large" => $array["song"]["cover_url"]
      ),
      "duration" => $array["song"]["duration"]
    );

    // add to output array
    $this->outputArray["song"] = $array;
  }

  /**
   * get the playlist
   * @param string $url
   * @param string $stationId
   * @param string $sessionId
   */
  function get($url, $stationId, $sessionId)
  {
    if (!$sessionId) {
      $sessionId = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 18);
    }

    $this->url = $url;
    $this->stationId = $stationId;
    $this->sessionId = $sessionId;

    // if no $stationId then fetch $stationId and $tracksCount
    if (empty($this->stationId)) {
      $this->getMixInfo();
    }

    // add the next or first song to $outputArray
    $this->nextSong();

    $this->output->successWithData($this->outputArray);
  }

}
