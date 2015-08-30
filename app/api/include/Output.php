<?php

/**
 * for managing output formats
 */
abstract class Output {

  protected $data = array("error"=>0, "status"=>"OK.");

  /**
   * output text with newline
   * @param string $text
   */
  function text($text) {
    echo $text.PHP_EOL;
  }

  /**
   * set error message and error number
   * @param string $message
   * @param integer $error
   */
  protected function setError($message, $error) {
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

class OutputArray extends Output {

  function error($message, $error = 1) {
    $this->setError($message, $error);
    die(print_r($this->data));
  }

  function success() {
    print_r($this->data);
  }

  function successWithData($data) {
    print_r(array_merge($this->data, $data));
  }
}

class OutputJSON extends Output {

  function error($message, $error = 1) {
    $this->setError($message, $error);
    die(json_encode($this->data).PHP_EOL);
  }

  function success() {
    echo json_encode($this->data).PHP_EOL;
  }

  function successWithData($data) {
    echo json_encode(array_merge($this->data, $data)).PHP_EOL;
  }
}
