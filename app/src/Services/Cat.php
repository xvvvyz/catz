<?php

namespace Omgcatz\Services;

class Cat
{
    const CAT_URL = 'http://thecatapi.com/src/images/get';

  /**
   * @var array
   */
  private $data;

  /**
   * @todo actually get random cat data (reddit?).
   */
  public function getCat()
  {
      $this->data['url'] = self::CAT_URL;

      return $this->data;
  }

    public function getCatUrl()
    {
        return self::CAT_URL;
    }
}
