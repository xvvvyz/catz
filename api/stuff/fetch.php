<?php

$url = (isset($_POST["url"]) ? $_POST["url"] : "cat");

if ($url) {
  $subdomains = array("m.", "www.", "mobile.");
  $host = str_ireplace($subdomains, "", parse_url($url, PHP_URL_HOST));
}

switch ($host) {
  case "8tracks.com":
    require "stuff/fetch/EightTracks.php";

    $mixId = (isset($_POST["mixId"]) ? $_POST["mixId"] : false);
    $trackNumber = (isset($_POST["trackNumber"]) ? $_POST["trackNumber"] : 0);

    $please = new EightTracks($output);
    $please->get($url, $mixId, $trackNumber);
    break;

  case "songza.com":
    require "stuff/fetch/Songza.php";

    $stationId = (isset($_POST["stationId"]) ? $_POST["stationId"] : false);
    $sessionId = (isset($_POST["sessionId"]) ? $_POST["sessionId"] : false);

    $please = new Songza($output);
    $please->get($url, $stationId, $sessionId);
    break;

  default:
    require "stuff/fetch/Cat.php";

    $please = new Cat($output);
    $please->getCat();
}
