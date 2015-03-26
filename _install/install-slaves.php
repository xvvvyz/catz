<?php

(PHP_SAPI !== "cli" || isset($_SERVER["HTTP_USER_AGENT"])) && die();

include "../api/include/Output.php";
include "../api/include/Database.php";

$db = new Database(new OutputArray());

$db->query("TRUNCATE TABLE slaves");

foreach ($db->slaves as $slave) {
  $db->query("INSERT INTO slaves (domain) VALUES ('$slave')");
}
