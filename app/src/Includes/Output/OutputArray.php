<?php

namespace Omgcatz\Includes\Output;

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
