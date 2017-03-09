<?php

namespace Czigor\Tinsight;

use Czigor\Tinsight\TinsightRequestBase;

/**
 * t-insight RateRequest implementation.
 */
class TinsightRateRequest extends TinsightRequestBase {

  public function __construct($live = FALSE, $credentials = []) {
    parent::__construct($live, $credentials);
    $this->requestType = 'rate';
  }

  protected function requestBodyXml(XMLWriter $writer) {

  }
}