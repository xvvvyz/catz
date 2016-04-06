<?php
namespace Omgcatz\Controller;

use Omgcatz\Includes\Archive;
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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
    /** @var Delegate $delegate */
    $delegate = $app['delegate'];

    /** @var Curl $curl */
    $curl = $app['curl'];

    if ($delegate->usingMinions()) {
      $server = $request->get('server', null);
      if (empty($server)) {
        $mixId = $request->get('mix_id', null);

        if (empty($mixId)) {
          return new JsonResponse(['error' => 'mix_id is empty.'], 400);
        }

        $server = $delegate->getServer($mixId, '8tracks_playlists');
      } else {
        if (!$delegate->verifyServer($server)) {
          return new JsonResponse(['error' => 'invalid server: ' . $server], 400);
        }
      }
      $results = $curl->post($server . '/archive', $request->request->all());
      $results['server'] = $server;
    } else {
      /** @var Archive $archive */
      $archive = $app['archive'];

      $results = $archive->execute($request->get('slug'), $request->get('download_id'));
    }

    return new JsonResponse($results);
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
      if (empty($server)) {
        $mixId = $request->get('mix_id', null);

        if (empty($mixId)) {
          return new JsonResponse(['error' => 'mix_id is empty.'], 400);
        }

        $server = $delegate->getServer($mixId, '8tracks_playlists');
      } else {
        if (!$delegate->verifyServer($server)) {
          return new JsonResponse(['error' => 'invalid server: ' . $server], 400);
        }
      }
      $results = $curl->post($server . '/download', $request->request->all());
      $results['server'] = $server;
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
          $eightTracks = $app['eightTracks'];
          $data = $eightTracks->get($url, $request->get('mix_id', false), $request->get('track_number', 0));
          break;

        case "songza.com":
          $songza = $app['songsa'];
          $data = $songza->get($url, $request->get('station_id', false), $request->get('session_id', false));
          break;

        default:
          $please = new Cat();
          $data = $please->getCat();
      }
      return new JsonResponse(array_merge(['error' => 0, 'status' => 'ok'], $data));
    } catch (ServiceException $e) {
      $app['monolog']->addError($e->getMessage());
      return new JsonResponse(['error' => $e->getStatusCode()]);
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

    $response = new BinaryFileResponse(sprintf('%s/%s', $app['app_dir'], $filePath));

    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    switch ($ext) {
      case "mp3":
        $response->headers->set('Content-Type', 'audio/mpeg3');
      case "m4a":
        $response->headers->set('Content-Type', 'audio/mp4');
      case "zip":
        $response->headers->set('Content-Type', 'application/x-compressed');

    }
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Expires', '0');
    $response->headers->set('Content-Disposition',
        $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName)
    );
    return $response;
  }
}
