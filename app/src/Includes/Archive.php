<?php

namespace Omgcatz\Includes;

use Symfony\Component\Process\Process;

class Archive
{
  /**
   * @param string $slug
   * @param string $downloadId
   * @return string
   */
  public function execute($slug, $downloadId)
  {
    $slug = escapeshellarg(preg_replace("~/~", "", $slug));
    $downloadId = escapeshellarg(preg_replace("~/~", "", $downloadId));

    $path = "archives/" . $slug . "/" . $downloadId;
    $fileName = $slug . ".zip";

    $process = new Process("cd $path && ../../../find . \! -name *.zip -exec ../../../zip -0 -D -r $fileName * \; -delete");
    $process->run();

    if (!$process->isSuccessful()) {
      throw new \RuntimeException($process->getErrorOutput());
    }

    return $process->getOutput();
  }
}
