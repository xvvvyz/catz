<?php

namespace Omgcatz\Includes;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class Download
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
   * @param array $args
   *
   * @return string
   */
  public function execute(array $args)
  {
    foreach ($args as $key => $value) {
      if (empty($value)) {
        unset($args[$key]);
      } else {
        $args[$key] = escapeshellarg(str_replace(array("`", chr(96)), array("'", "'"), html_entity_decode(trim($value)))) . PHP_EOL;
      }
    }

    $process = new Process("cd $this->cwd && ./download.sh ". json_encode($args, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE));
    $process->run();

    if (!$process->isSuccessful()) {
      throw new \RuntimeException($process->getErrorOutput());
    }

    return $process->getOutput();
  }
}
