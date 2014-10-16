<?php

class Curl {

  private $curl;

  /**
   * Destructor.
   */
  function __destruct() {
    // Close connection to curl.
    curl_close($this->curl);
  }

  /**
   * Return array from JSON URL.
   * @param string $url
   * @return array
   */
  function returnTransfer($url) {
    $this->curl = curl_init($url);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

    return json_decode(curl_exec($this->curl), true);
  }
}
