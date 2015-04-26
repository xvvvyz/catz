<?php

include "include/Output.php";

$debugging = false;
$testing = false;
$dataType = (isset($_POST["dataType"]) ? $_POST["dataType"] : "json");

// Are we debugging?
if ($debugging) {
  ini_set("display_errors", "On");
  error_reporting(E_ALL | E_STRICT);
} else {
  ini_set("display_errors", "Off");
  error_reporting(0);
}

// Are we testing?
if ($testing) {
  //$_POST = array(
  //  "what" => "download",
  //  "song_id" => "324523423",
  //  "tag_song_title" => 1,
  //  "song_title" => "song_title",
  //  "song_artist" => "song_artist",
  //  "song_album" => "song_album",
  //  "song_artwork" => "http://www.online-image-editor.com/styles/2014/images/example_image.png",
  //  // "song_genre" => "song_genre",
  //  "song_url" => "http://cft.8tracks.com/tf/085/313/618/O2GbFU.48k.v3.m4a",
  //  "song_number" => 6,
  //  "total_songs" => 16,
  //  // "mix_artwork" => "http://upload.wikimedia.org/wikipedia/commons/b/bf/GOES-13_First_Image_jun_22_2006_1730Z.jpg",
  //  "mix_slug" => "mix_slug",
  //  // "recursive" => 1,
  //  // "itunes_compilation" => 1,
  //  "download_id" => "download_id",
  //);

  //$_POST = array(
  //  "what" => "fetch",
  //  "url" => "http://8tracks.com/leonardo-palhano/we-can-dance-in-desire"
  //);
}

// Set output type.
switch ($dataType) {
  case "array":
    $output = new OutputArray();
    break;
  
  default:
    $output = new OutputJSON();
}

// Do what?
if (isset($_POST["what"])) {
  $what = $_POST["what"];
  switch ($what) {
    case "archive":
    case "download":
      include "stuff/route.php";
      break;

    case "fetch":
      include "stuff/fetch.php";
      break;

    default:
      $output->error($what." is not a valid thing to do...");
  }
} else {
  $output->error("What are we doing?");
}
