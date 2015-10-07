<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
$loader->parse();
$config = $loader->toArray();

date_default_timezone_set($config['TIMEZONE'] ?: 'Europe/Berlin');

$config = array_merge($config, ['app_dir' => __DIR__]);

$app = new Omgcatz\App($config);
return $app;
