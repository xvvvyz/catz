<?php

use Symfony\Component\HttpFoundation\Request;

$loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
$loader->parse();
$loader->putenv(true);

date_default_timezone_set(getenv('TIMEZONE') ?: 'Europe/Berlin');

$app['debug'] = getenv('DEBUG');
$app['env'] = $app['debug'] ? 'dev' : 'prod';

if ($app['debug']) {
  ini_set("display_errors", "On");
  error_reporting(E_ALL | E_STRICT);
} else {
  ini_set("display_errors", "Off");
  error_reporting(0);
}

$app->register(new Silex\Provider\TwigServiceProvider(), [
  'twig.path' => __DIR__ . '/views',
  'twig.options' => [
    'debug' => $app['debug'],
    'cache' => __DIR__ . sprintf("/cache/%s/twig", $app['env'])
  ]
]);

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Add asset pipeline
 */
$app['twig'] = $app->share($app->extend('twig', function (Twig_Environment $twig) {
  $twig->addFunction(new Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/%s', ltrim($asset, '/'));
  }));

  return $twig;
}));

/**
 * Register logging
 */
$app->register(new Silex\Provider\MonologServiceProvider(), [
  'monolog.logfile' => __DIR__ . sprintf("/logs/%s.log", $app['env']),
]);


/**
 * Routing land
 */

$app->get('/', function () use ($app) {
  return $app['twig']->render('index.html.twig');
})->bind('home');

$app->post('/archive', function (Request $request) {

})->bind('archive');

$app->post('/download', function (Request $request) {

})->bind('download');

$app->post('/fetch', function (Request $request) {

})->bind('fetch');




