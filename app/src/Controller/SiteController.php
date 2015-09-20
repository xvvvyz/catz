<?php
namespace Omgcatz\Controller;

use Omgcatz\Includes\Curl;
use Omgcatz\Includes\Delegate;
use Omgcatz\Includes\Download;
use Omgcatz\Services\Cat;
use Omgcatz\Services\EightTracks;
use Omgcatz\Services\Exceptions\ServiceException;
use Omgcatz\Services\Songza;
use Silex\Application;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController
{
  /**
   * Index action.
   *
   * @param Application $app
   * @return Response
   */
  public function indexAction(Application $app)
  {
    return $app['twig']->render('index.html.twig');
  }

  /**
   * Archive action.
   *
   * @param Application $app
   * @param Request $request
   * @return JsonResponse
   */
  public function archiveAction(Application $app, Request $request)
  {
    // TODO: Fix up this action

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
  }

  /**
   * Download action.
   *
   * @param Application $app
   * @param Request $request
   * @return JsonResponse
   */
  public function downloadAction(Application $app, Request $request)
  {
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
      /** @var Download $download */
      $download = $app['download'];

      $results = $download->execute($request->request->all());
      $results['server'] = '';
    }

    return new JsonResponse($results);
  }

  /**
   * Fetch action.
   *
   * @param Application $app
   * @param Request $request
   * @return JsonResponse
   */
  public function fetchAction(Application $app, Request $request)
  {
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
      return new JsonResponse(array_merge(['error' => 0, 'status' => 'ok'], $data));
    } catch (ServiceException $e) {
      return new JsonResponse(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Magic action.
   *
   * @param Application $app
   * @param Request $request
   * @return BinaryFileResponse
   */
  public function magicAction(Application $app, Request $request)
  {
    $filePath = $request->get('p');
    $fileName = $request->get('s');

    $response = new BinaryFileResponse(sprintf('%s/%s', $app['app_dir'] ,$filePath));

    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    switch ($ext) {
      case "mp3":
      case "m4a":
      case "zip":
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Length', filesize($filePath));
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $fileName));
    }

    return $response;
  }
}
