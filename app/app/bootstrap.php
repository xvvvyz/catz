<?php

use Omgcatz\Includes\Archive;
use Omgcatz\Includes\Curl;
use Omgcatz\Includes\Database;
use Omgcatz\Includes\Delegate;
use Omgcatz\Includes\Download;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Provider\ConsoleServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
$loader->parse();
$loader->putenv(true);

date_default_timezone_set(getenv('TIMEZONE') ?: 'Europe/Berlin');

$app = new Silex\Application();

$app['debug'] = getenv('DEBUG') ?: false;
$app['env'] = $app['debug'] ? 'dev' : 'prod';

$app['app_dir'] = __DIR__;
$app['app_download_dir'] = __DIR__ . '/download';

if ($app['debug']) {
  ini_set("display_errors", "On");
  error_reporting(E_ALL | E_STRICT);
} else {
  ini_set("display_errors", "Off");
  error_reporting(0);
}

/**
 * Register database
 */
$app['database'] = $app->share(function () {
  return new Database(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
});

/**
 * Register curl
 */
$app['curl'] = function () {
  return new Curl();
};

/**
 * Register Delegate
 */
$app['delegate'] = function () use ($app) {
  return new Delegate($app['database']);
};

/**
 * Register Download
 */
$app['download'] = $app->share(function () use ($app) {
  return new Download($app['app_dir']);
});

$app['archive'] = $app->share(function () use ($app) {
  return new Archive($app['app_dir']);
});

/**
 * Command line land
 */
$app->register(new ConsoleServiceProvider(), array(
  'console.name' => 'OmgCatz',
  'console.version' => '1.0.0',
  'console.project_directory' => __DIR__ . '/..'
));

/**
 * Register Twig
 */
$app->register(new Silex\Provider\TwigServiceProvider(), [
  'twig.path' => __DIR__ . '/views',
  'twig.options' => [
    'debug' => $app['debug'],
    'cache' => __DIR__ . sprintf("/cache/%s/twig", $app['env'])
  ]
]);

/**
 * Add URL generation
 */
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
 * Register error handler
 */
$app->error(function (\Exception $e, $code) {
  $code = 500;
  if ($e instanceof FileNotFoundException) {
    $code = 404;
  }
  return new JsonResponse(['error' => $e->getMessage()], $code);
});

/**
 * Routing land
 */
$app->get('/', '\Omgcatz\Controller\SiteController::indexAction')->bind('home');
$app->post('/archive', '\Omgcatz\Controller\SiteController::archiveAction')->bind('archive');
$app->post('/download', '\Omgcatz\Controller\SiteController::downloadAction')->bind('download');
$app->post('/fetch', '\Omgcatz\Controller\SiteController::fetchAction')->bind('fetch');
$app->get('/magic', '\Omgcatz\Controller\SiteController::magicAction')->bind('magic');

return $app;
