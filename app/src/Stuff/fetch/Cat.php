<?php

class Cat {

  private $output;

  public function __construct($output) {
    $this->output = $output;
  }

  /**
   * @todo actually get random cat data (reddit?).
   */
  function getCat() {
    $this->data["url"] = "http://thecatapi.com/src/images/get";
    $this->output->successWithData($this->data);
  }

  function getCatUrl() {
    $this->output->text("http://thecatapi.com/src/images/get");
  }

}
