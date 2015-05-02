<?php

if (isset($_POST["url"])) {
  $url = $_POST["url"];
  $subdomains = array("m.", "www.", "mobile.");
  $host = str_ireplace($subdomains, "", parse_url($url, PHP_URL_HOST));
}

switch ($host) {
  case "8tracks.com":
    require "stuff/fetch/EightTracks.php";

    $mixId = (isset($_POST["mix_id"]) ? $_POST["mix_id"] : false);
    $trackNumber = (isset($_POST["track_number"]) ? $_POST["track_number"] : 0);

    $please = new EightTracks($output);
    $please->get($url, $mixId, $trackNumber);
    break;

  case "songza.com":
    require "stuff/fetch/Songza.php";

    $stationId = (isset($_POST["station_id"]) ? $_POST["station_id"] : false);
    $sessionId = (isset($_POST["session_id"]) ? $_POST["session_id"] : false);

    $please = new Songza($output);
    $please->get($url, $stationId, $sessionId);
    break;

  default:
    require "stuff/fetch/Cat.php";

    $please = new Cat($output);
    $please->getCat();
}
