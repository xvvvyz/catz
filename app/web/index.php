<?php

/**
 * Fix for CLI PHP and static assets.
 */
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
  return false;
}
$app = require __DIR__ . '/../app/bootstrap.php';
$app->run();
