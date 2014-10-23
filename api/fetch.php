<?php

include "include/Output.php";
include "fetch/EightTracks.php";
include "fetch/Songza.php";
include "fetch/Cat.php";

$debugging = false;
$dataType = (isset($_POST["dataType"]) ? $_POST["dataType"] : "json");
$url = (isset($_POST["url"]) ? $_POST["url"] : "cat");

// Are we debugging?
if ($debugging) {
  ini_set("display_errors", "On");
  error_reporting(E_ALL | E_STRICT);
} else {
  ini_set("display_errors", "Off");
  error_reporting(0);
}

// Set output type to dataType.
switch ($dataType) {
  case "array":
    $output = new OutputArray();
    break;
  
  default:
    $output = new OutputJSON();
}

// Get URL and hostname.
if ($url) {
  $subdomains = array("m.", "www.", "mobile.");
  $host = str_ireplace($subdomains, "", parse_url($url, PHP_URL_HOST));
}

// Go get stuff.
switch ($host) {
  case "8tracks.com":
    $mixId = (isset($_POST["mixId"]) ? $_POST["mixId"] : false);
    $playToken = (isset($_POST["playToken"]) ? $_POST["playToken"] : false);
    $trackNumber = (isset($_POST["trackNumber"]) ? $_POST["trackNumber"] : 0);

    $please = new EightTracks($output);
    $please->get(
      $url,
      $mixId,
      $playToken,
      $trackNumber
    );
    break;

  case "songza.com":
    $stationId = (isset($_POST["stationId"]) ? $_POST["stationId"] : false);
    $sessionId = (isset($_POST["sessionId"]) ? $_POST["sessionId"] : false);

    $please = new Songza($output);
    $please->get(
      $url,
      $stationId,
      $sessionId
    );
    break;

  default:
    $please = new Cat($output);
    $please->getCat();
}
