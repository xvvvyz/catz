<?php

include_once("include/Database.php");

class Cat extends Database {

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
