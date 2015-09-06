<?php

use Omgcatz\Includes\Curl;
use Omgcatz\Includes\Database;
use Omgcatz\Includes\Delegate;
use Omgcatz\Services\Cat;
use Omgcatz\Services\EightTracks;
use Omgcatz\Services\Exceptions\ServiceException;
use Omgcatz\Services\Songza;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * Setup Delegate
 */
$app['delegate'] = function () use ($app) {
  return new Delegate($app['database']);
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

$app->post('/archive', function (Request $request) use ($app) {

  /** @var Delegate $delegate */
  $delegate = $app['delegate'];

  /** @var Curl $curl */
  $curl = $app['curl'];

  if ($delegate->usingMinions()) {
    $server = $request->get('server', null);
    if ($server === null) {
      $mixId = $request->get('mix_id', null);

      if ($mixId === null) {
        return new JsonResponse(['error' => 'mix_id is empty.'], 400);

      }

      $delegate->getServer($mixId, '8tracks_playlists');
    } else {
      if (!$delegate->verifyServer($server)) {
        return new JsonResponse(['error' => 'invalid server: ' . $server], 400);
      }
    }
    $results = $curl->post($server . 'archive.php', $_POST);
  } else {
    $server = "/src/Stuff/download/";

    $results = $curl->localPost(__DIR__ . "/download", 'archive.php');
  }

})->bind('archive');

$app->post('/download', function (Request $request) use ($app) {

  /** @var Delegate $delegate */
  $delegate = $app['delegate'];

  /** @var Curl $curl */
  $curl = $app['curl'];

  if ($delegate->usingMinions()) {
    $server = $request->get('server', null);
    if ($server === null) {
      $mixId = $request->get('mix_id', null);

      if ($mixId === null) {
        return new JsonResponse(['error' => 'mix_id is empty.'], 400);
      }

      $delegate->getServer($mixId, '8tracks_playlists');
    } else {
      if (!$delegate->verifyServer($server)) {
        return new JsonResponse(['error' => 'invalid server: ' . $server], 400);
      }
    }
    $results = $curl->post($server . 'download.php', $_POST);
  } else {
    $server = "/src/Stuff/download/";

    $results = $curl->localPost(__DIR__ . "/download", 'download.php');
  }
})->bind('download');

$app->post('/fetch', function (Request $request) use ($app) {

  $url = $request->get('url', null);

  try {
    if ($url === null) {
      throw new ServiceException('No URL provided');
    }
    $subDomains = array('m.', 'www.', 'mobile.');
    $host = str_ireplace($subDomains, '', parse_url($url, PHP_URL_HOST));

    $data = null;

    switch ($host) {
      case "8tracks.com":
        $please = new EightTracks($app['database'], $app['curl']);
        $data = $please->get($url, $request->get('mix_id', false), $request->get('track_number', 0));
        break;

      case "songza.com":
        $please = new Songza($app['curl']);
        $data = $please->get($url, $request->get('station_id', false), $request->get('session_id', false));
        break;

      default:
        $please = new Cat();
        $data = $please->getCat();
    }
    return new JsonResponse($data);
  } catch (ServiceException $e) {
    return new JsonResponse(['error' => $e->getMessage()], 400);
  }
})->bind('fetch');

return $app;



