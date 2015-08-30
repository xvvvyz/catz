<?php

(PHP_SAPI !== "cli" || isset($_SERVER["HTTP_USER_AGENT"])) && die();

include "../api/include/Database.php";

$db = new Database();

$db->simpleQuery("TRUNCATE TABLE minions");

foreach (Config::$minions as $minion) {
  $db->simpleQuery("INSERT INTO minions (`minionRoot`,`load`) VALUES ('{$minion}',0)");
}
