<?php

// master if mix already has slave use that slave
// else check database to see if has slave
// else select the oldest slave
// master detect if slave exists
// if not - blacklist for x time
// else - send data
// return status


require "include/Database.php";

$database = new Database();
$usingSlaves = (!empty($database->slaves));

if (empty($_POST["server"])) {
  if ($usingSlaves) {
    //$slave = $database->fetchRows("SELECT slaveId FROM 8tracks_playlists WHERE mixId='".$_POST["id"]."' LIMIT 1");

    if (!$slave) {
      $slave = array_rand($database->slaves);
    }
    
    $server = $database->slaves[$slave];
    //$database->query("INSERT INTO 8tracks_playlists (slaveId) VALUES ('".$slave."')");
  } else {
    $server = "/api/stuff/download/";
  }
} else {
  $server = $_POST["server"];
}

switch ($_POST["what"]) {
  case "download":
    $script = "download.php";
    break;

  case "archive":
    $script = "archive.php";
    break;
}

if (isset($script)) {
  if ($usingSlaves) {
    require "include/Curl.php";
    $curl = new Curl();
    $results = $curl->post($server.$script, $_POST);
  } else {
    chdir(__DIR__."/download");
    ob_start();
    require $script;
    $results = ob_get_clean();
  }
}

$results = json_decode($results, true);
$results["server"] = $server;
$output->successWithData($results);
