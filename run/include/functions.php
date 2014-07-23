<?php

function command_exist($cmd) {
  $returnVal = shell_exec("which $cmd");
  return (empty($returnVal) ? false : true);
}

function bail_out($code, $message) {
	exit(json_encode(array('error' => $code, 'message' => $message)));
}