<?php

include_once("include/Database.php");
include_once("include/Curl.php");

class Songza extends Database {

  // Mix info.
  private $url;
  private $stationId;
  private $sessionId;

  // Output array.
  private $outputArray = array();

  /**
   * Get mix info from URL.
   */
  private function getMixInfo() {
    $curl = new Curl();
    $source = $curl->returnSource($this->url);

    // Grab station ID.
    preg_match_all('/data-sz-station-id="[^"]*/', $source, $matches);
    list(,$this->stationId) = explode('"', $matches[0][0]);

    // Check for no station ID.
    if (empty($this->stationId)) {
      $this->output->error("Invalid URL: ".$this->url);
    }

    // Grab station info.
    $array = $curl->returnArray("http://songza.com/api/1/station/".$this->stationId);

    // Check for failed info grab.
    if ($array["status"] != "NORMAL") {
      $this->output->error("Songza said: ".$array["status"]);
    }

    // Put info into array.
    $array = array(
      "id"=>$this->stationId,
      "sessionId"=>$this->sessionId,
      "slug"=>$array["dasherized_name"],
      "name"=>$array["name"],
      "imgUrl"=>array(
        "small"=>"http://songza.com/api/1/station/".$this->stationId."/image?size=133",
        "medium"=>"http://songza.com/api/1/station/".$this->stationId."/image?size=500&name=image.jpg"
      ),
      "creator"=>$array["creator_name"],
      "totalTracks"=>$array["song_count"]
    );

    // Add to output array.
    $this->outputArray["mix"] = $array;
  }

  /**
   * Get song info from URL.
   */
  private function getNextSong() {
    // Grab song info.
    $curl = new Curl();
    $array = $curl->returnArray("http://songza.com/api/1/station/".$this->stationId."/next", "sessionid=".$this->sessionId."; visitor-prompted:1");

    // Clean up array.
    $array = array(
      "title"=>$array["song"]["title"],
      "artist"=>$array["song"]["artist"]["name"],
      "album"=>$array["song"]["album"],
      "genre"=>$array["song"]["genre"],
      "url"=>$array["listen_url"],
      "coverUrls"=>array(
        "small"=>$array["song"]["cover_url"],
        "medium"=>$array["song"]["cover_url"]
      ),
      "duration"=>$array["song"]["duration"],
      "id"=>$array["song"]["id"]
    );

    // Add to output array.
    $this->outputArray["song"] = $array;
  }

  /**
   * Get the playlist.
   * @param string $url
   * @param string $stationId
   * @param string $sessionId
   */
  function get($url, $stationId, $sessionId) {
    if (!$sessionId) {
      $sessionId = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 18);
    }

    $this->url = $url;
    $this->stationId = $stationId;
    $this->sessionId = $sessionId;

    // If no $stationId then fetch $stationId and $tracksCount.
    if (empty($this->stationId)) {
      $this->getMixInfo();
    }

    // Add the next or first song to outputArray.
    $this->getNextSong();

    $this->output->successWithData($this->outputArray);
  }
}
