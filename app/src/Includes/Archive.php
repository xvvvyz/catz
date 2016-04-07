<?php

namespace Omgcatz\Includes;

use Symfony\Component\Process\Process;

class Archive
{
    /**
   * @var string
   */
  private $cwd;

  /**
   * @param string $cwd
   */
  public function __construct($cwd)
  {
      $this->cwd = $cwd;
  }

  /**
   * @param string $slug
   * @param string $downloadId
   *
   * @return bool
   */
  public function execute($slug, $downloadId)
  {
      $slug = preg_replace('~/~', '', $slug);
      $downloadId = preg_replace('~/~', '', $downloadId);

      $path = $this->cwd.'/download/archives/'.$slug.'/'.$downloadId;
      $path = escapeshellarg($path);

      $fileName = $slug.'.zip';
      $process = new Process("cd $path && find . ! -name *.zip -exec zip -0 -D -r $fileName * \; -delete");
      $process->run();

      if (!$process->isSuccessful()) {
          throw new \RuntimeException($process->getErrorOutput());
      }

      return true;
  }
}
