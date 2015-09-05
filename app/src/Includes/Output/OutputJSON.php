<?php

namespace Omgcatz\Includes\Output;

class OutputJSON extends Output
{
  function error($message, $error = 1)
  {
    $this->setError($message, $error);
    die(json_encode($this->data) . PHP_EOL);
  }

  function success()
  {
    echo json_encode($this->data) . PHP_EOL;
  }

  function successWithData($data)
  {
    echo json_encode(array_merge($this->data, $data)) . PHP_EOL;
  }
}
