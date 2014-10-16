<?php

abstract class Output {

  protected $data = array("error"=>0, "status"=>"OK.");

  /**
   * Output text with newline.
   * @param string $text
   */
  function text($text) {
    echo $text.PHP_EOL;
  }

  /**
   * Output error message and bail.
   * @param string $message
   */
  abstract function error($message);

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

  function error($message) {
    $this->data["error"] = 1;
    $this->data["status"] = $message;
    
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

  function error($message) {
    $this->data["error"] = 1;
    $this->data["status"] = $message;
    
    die(json_encode($this->data).PHP_EOL);
  }

  function success() {
    echo json_encode($this->data).PHP_EOL;
  }

  function successWithData($data) {
    echo json_encode(array_merge($this->data, $data)).PHP_EOL;
  }
}
