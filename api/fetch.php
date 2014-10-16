<?php

include "include/Output.php";
include "fetch/Cat.php";
include "fetch/EightTracks.php";
include "fetch/Songza.php";

// $_POST["dataType"] = "json";
// $_POST["url"] = "http://8tracks.com/carmarie/in-love-with-the-eighties"; 
// $_POST["mixId"] = "4908272";
// $_POST["playToken"] = "1140831196";
// $_POST["trackNumber"] = 6;

// Set output type to dataType.
switch ($_POST["dataType"]) {
  case "array":
    $output = new OutputArray();
    break;
  
  default:
    $output = new OutputJSON();
}

// Get URL and hostname.
if (isset($_POST["url"])) {
  $url = $_POST["url"];
  $subdomains = array("m.", "www.", "mobile.");
  $host = str_ireplace($subdomains, "", parse_url($url, PHP_URL_HOST));
}

// Do stuff.
switch ($host) {
  case "8tracks.com":
    $please = new EightTracks($output);
    $please->getPlaylist(
      $url,
      $_POST["mixId"],
      $_POST["playToken"],
      $_POST["trackNumber"]
    );
    break;

  case "songza.com":
    $output->error("Songza will be supported soon.");
    break;

  default:
    $please = new Cat($output);
    $please->getCat();
}
