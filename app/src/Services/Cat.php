<?php

namespace Omgcatz\Services;

use Omgcatz\Includes\Output\Output;

class Cat {

  /**
   * @var Output
   */
  private $output;

  public function __construct(Output $output) {
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
