<?php

use Omgcatz\Includes\Database;
use Symfony\Component\HttpFoundation\Request;
use Knp\Provider\ConsoleServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
$loader->parse();
$loader->putenv(true);

date_default_timezone_set(getenv('TIMEZONE') ?: 'Europe/Berlin');

$app = new Silex\Application();

$app['debug'] = getenv('DEBUG');
$app['env'] = $app['debug'] ? 'dev' : 'prod';

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
$app['database'] = function() {
  return new Database(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
};

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

  $url = $request->get('url', null);
  if ($url !== null) {
    $subdomains = array("m.", "www.", "mobile.");
    $host = str_ireplace($subdomains, "", parse_url($url, PHP_URL_HOST));

    switch ($host) {
      case "8tracks.com":

        $mixId = (isset($_POST["mix_id"]) ? $_POST["mix_id"] : false);
        $trackNumber = (isset($_POST["track_number"]) ? $_POST["track_number"] : 0);

        $please = new EightTracks($output);
        $please = new EightTracks($output);
        $please->get($url, $mixId, $trackNumber);
        break;

      case "songza.com":


        $stationId = (isset($_POST["station_id"]) ? $_POST["station_id"] : false);
        $sessionId = (isset($_POST["session_id"]) ? $_POST["session_id"] : false);

        $please = new Songza($output);
        $please->get($url, $stationId, $sessionId);
        break;

      default:

        $please = new Cat($output);
        $please->getCat();
    }
  }


})->bind('fetch');

return $app;



