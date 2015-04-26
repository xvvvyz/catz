<?php

(PHP_SAPI !== "cli" || isset($_SERVER["HTTP_USER_AGENT"])) && die();

include "../api/include/Database.php";

$db = new Database();

$db->simpleQuery("TRUNCATE TABLE slaves");

foreach (Config::$slaves as $slave) {
  $db->simpleQuery("INSERT INTO slaves (`slaveRoot`,`load`) VALUES ('{$slave}',0)");
}
