<?php

namespace Omgcatz\Includes\Output;

/**
 * for managing output formats
 */
abstract class Output
{
  protected $data = array("error" => 0, "status" => "OK.");

  /**
   * output text with newline
   * @param string $text
   */
  function text($text)
  {
    echo $text . PHP_EOL;
  }

  /**
   * set error message and error number
   * @param string $message
   * @param integer $error
   */
  protected function setError($message, $error)
  {
    if ($error === NULL) {
      $error = 1;
    }

    $this->data["error"] = $error;
    $this->data["status"] = $message;
  }

  /**
   * output error and bail
   * @param string $message
   * @param integer $error
   */
  abstract function error($message, $error = 1);

  /**
   * output success
   */
  abstract function success();

  /**
   * output success with data
   * @param array $data
   */
  abstract function successWithData($data);
}
