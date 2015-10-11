<?php

namespace Omgcatz;

use Knp\Provider\ConsoleServiceProvider;
use Omgcatz\Includes\Archive;
use Omgcatz\Includes\Curl;
use Omgcatz\Includes\Database;
use Omgcatz\Includes\Delegate;
use Omgcatz\Includes\Download;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig_Environment;
use Twig_SimpleFunction;

class App extends Application
{
  /**
   * @param array $config
   */
  public function __construct(array $config = []) {
    parent::__construct();
    $this['debug'] = isset($config['DEBUG']) ? $config['DEBUG'] : false;
    $this['env'] = $this['debug'] ? 'dev' : 'prod';

    $this['app_dir'] = $config['app_dir'];
    $this['app_download_dir'] = $this['app_dir'] . '/download';

    if ($this['debug']) {
      ini_set("display_errors", "On");
      error_reporting(E_ALL | E_STRICT);
    } else {
      ini_set("display_errors", "Off");
      error_reporting(0);
    }

    /**
     * Register database
     */
    $this['database'] = $this->share(function () use ($config) {
      return new Database($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
    });

    /**
     * Register curl
     */
    $this['curl'] = function () {
      return new Curl();
    };

    /**
     * Register Delegate
     */
    $this['delegate'] = function () {
      return new Delegate($this['database']);
    };

    /**
     * Register Download
     */
    $this['download'] = $this->share(function ()  {
      return new Download($this['app_dir']);
    });

    $this['archive'] = $this->share(function () {
      return new Archive($this['app_dir']);
    });

    /**
     * Command line land
     */
    $this->register(new ConsoleServiceProvider(), [
      'console.name' => 'OmgCatz',
      'console.version' => '1.0.0',
      'console.project_directory' => $this['app_dir'] . '/..'
    ]);

    /**
     * Register Twig
     */
    $this->register(new TwigServiceProvider(), [
      'twig.path' =>  $this['app_dir'] . '/views',
      'twig.options' => [
        'debug' => $this['debug'],
        'cache' =>  $this['app_dir'] . sprintf("/cache/%s/twig", $this['env'])
      ]
    ]);

    /**
     * Add URL generation
     */
    $this->register(new UrlGeneratorServiceProvider());

    /**
     * Add asset pipeline
     */
    $this['twig'] = $this->share($this->extend('twig', function (Twig_Environment $twig) {
      $twig->addFunction(new Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('/%s', ltrim($asset, '/'));
      }));

      return $twig;
    }));

    /**
     * Register logging
     */
    $this->register(new MonologServiceProvider(), [
      'monolog.logfile' => $this['app_dir'] . sprintf("/logs/%s.log", $this['env']),
    ]);

    /**
     * Register error handler
     */
    $this->error(function (\Exception $e, $code) {
      $code = 500;
      if ($e instanceof FileNotFoundException) {
        $code = 404;
      }
      return new JsonResponse(['error' => $e->getMessage()], $code);
    });

    /**
     * Routing land
     */
    $this->get('/', '\Omgcatz\Controller\SiteController::indexAction')->bind('home');
    $this->post('/archive', '\Omgcatz\Controller\SiteController::archiveAction')->bind('archive');
    $this->post('/download', '\Omgcatz\Controller\SiteController::downloadAction')->bind('download');
    $this->post('/fetch', '\Omgcatz\Controller\SiteController::fetchAction')->bind('fetch');
    $this->get('/magic', '\Omgcatz\Controller\SiteController::magicAction')->bind('magic');
  }
}
