<?php

require_once "Include/Delegate.php";
require_once "Include/Curl.php";

$delegate = new Delegate();
$curl = new Curl();

if ($delegate->usingMinions()) {
  if (empty($_POST["server"])) {
    if (!empty($_POST["mix_id"])) {
      $mixId = $_POST["mix_id"];
    } else {
      $output->error("mix_id is empty.");
    }

    // TODO: need to set this dynamically for songza etc. support
    $table = "8tracks_playlists";

    $server = $delegate->getServer($mixId, $table);
  } else {
    $server = $_POST["server"];

    if (!$delegate->verifyServer($server)) {
      $output->error("invalid server: ".$server);
    }
  }

  $results = $curl->post($server.$script, $_POST);
} else {
  $server = "/src/Stuff/download/";

  $results = $curl->localPost(__DIR__."/download", $script);
}

$results = json_decode($results, true);
$results["server"] = $server;
$output->successWithData($results);
