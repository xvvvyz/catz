<?php

class Curl {

  private $ch;
  private $defaultUa = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:33.0) Gecko/20100101 Firefox/33.0";

  /**
   * Destructor.
   */
  function __destruct() {
    // Close connection to ch.
    curl_close($this->ch);
  }

  /**
   * Return source from URL.
   * @param string $url
   * @param string $cookie
   * @return string
   */
  function get($url, $cookie = "") {
    $userAgent = (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : $this->defaultUa);

    $this->ch = curl_init($url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);

    return curl_exec($this->ch);
  }

  /**
   * Return array from JSON URL.
   * @param string $url
   * @param string $cookie
   * @return array
   */
  function getArray($url, $cookie = "") {
    return json_decode($this->get($url, $cookie), true);
  }

  /**
   * Return results of a POST request.
   * @param string $url
   * @param array $postData
   * @return string
   */
  function post($url, $postData) {
    $payload = "";

    foreach($postData as $key=>$value) {
      $payload .= $key.'='.urlencode($value).'&';
    }

    $payload = rtrim($payload, '&');

    $this->ch = curl_init($url);

    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_POST, 1);
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);

    return curl_exec($this->ch);
  }

}
