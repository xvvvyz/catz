<?php

namespace Omgcatz\Services\Exceptions;

use Exception;

class ServiceException extends Exception
{
  /**
   * @var int
   */
  private $statusCode;

  /**
   * @param string $message
   * @param int $statusCode
   */
  public function __construct($message, $statusCode = 400)
  {
    parent::__construct($message);
    $this->statusCode = $statusCode;
  }

  /**
   * @return int
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
}
