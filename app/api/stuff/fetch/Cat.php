<?php

class Cat {

  // Output object.
  private $output;

  /**
   * Constructor.
   * @param object $output
   */
  public function __construct($output) {
    $this->output = $output;
  }

  /**
   * Get cat.
   * @todo Actually get random cat data (reddit?).
   * @return array Cat data.
   */
  function getCat() {
    $this->data["url"] = "http://thecatapi.com/api/images/get";
    $this->output->successWithData($this->data);
  }

  /**
   * Get cat url.
   * @todo Actually get a random cat url.
   * @return string Image url.
   */
  function getCatUrl() {
    $this->output->text("http://thecatapi.com/api/images/get");
  }

}
