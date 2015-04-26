<?php

require "include/Database.php";

$db = new Database();
$usingSlaves = (!empty(Config::$slaves));

if (empty($_POST["server"])) {
  if ($usingSlaves) {
    if (isset($_POST["mix_id"])) {
      $mixId = $_POST["mix_id"];
    } else {
      $output->error();
    }

    $table = "8tracks_playlists"; // need to set this dynamically for songza support
    
    $playlist = $db->select(
      "SELECT slaveId FROM {$table} WHERE mixId=? LIMIT 1",
      array($mixId),
      array("%d")
    );

    if (empty($playlist[0]["slaveId"])) {
      // Currently we are selecting a random slave, this should be a fancy algorithm.
      $slave = $db->query("SELECT * FROM `slaves` ORDER BY RAND() LIMIT 1");

      $slaveId = $slave[0]["slaveId"];
      $server = $slave[0]["slaveRoot"];

      // Set mix slave.
      $db->update(
        $table,
        array("slaveId" => $slaveId),
        array("%d"),
        array("mixId" => $mixId),
        array("%d")
      );
    } else {
      $slaveId = $playlist[0]["slaveId"];

      $slave = $db->select(
        "SELECT slaveRoot FROM slaves WHERE slaveId=?",
        array($slaveId),
        array("%d")
      );

      $server = $slave[0]["slaveRoot"];
    }
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
