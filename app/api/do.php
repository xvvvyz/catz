<?php

require_once "include/Output.php";

$debugging = (isset($debugging) ? $debugging : false);
$dataType = (isset($_POST["datatype"]) ? $_POST["datatype"] : "json");

// Are we debugging?
if ($debugging) {
  ini_set("display_errors", "On");
  error_reporting(E_ALL | E_STRICT);
} else {
  ini_set("display_errors", "Off");
  error_reporting(0);
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
      $script = $what.".php";
      require "stuff/delegate.php";
      break;

    case "fetch":
      require "stuff/fetch.php";
      break;

    default:
      $output->error($what." is not a valid thing to do...");
  }
} else {
  $output->error("What are we doing?");
}
