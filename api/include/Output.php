<?php

abstract class Output {

  // Output data.
  protected $data = array("error"=>0, "status"=>"OK.");

  /**
   * Output text with newline.
   * @param string $text
   */
  function text($text) {
    echo $text.PHP_EOL;
  }

  /**
   * Set error message and error.
   * @param string $message
   * @param int $error
   */
  function setError($message, $error) {
    if ($error === NULL) {
      $error = 1;
    }

    $this->data["error"] = $error;
    $this->data["status"] = $message;
  }

  /**
   * Output error message and bail.
   * @param string $message
   */
  abstract function error($message, $error = 1);

  /**
   * Output success message.
   */
  abstract function success();

  /**
   * Output success message and data.
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
