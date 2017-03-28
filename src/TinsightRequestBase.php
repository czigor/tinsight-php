<?php

namespace Czigor\Tinsight;

define('TINSIGHT_TEST_REQUEST_URL', 'https://staging.sgiws.com/api/gateway.cfc');
define('TINSIGHT_LIVE_REQUEST_URL', 'https://sgiws.com/api/gateway.cfc');

abstract class TinsightRequestBase {

  protected $live = FALSE;

  protected $credentials;

  protected $requestType;

  public function __construct($live = FALSE, TinsightCredentialsInterface $credentials, $request_type = 'rate') {
    $this->live = $live;
    $this->credentials = $credentials;
    $this->requestType = $request_type;
  }

  protected function requestXml() {
    $writer = new \XMLWriter();
    $writer->openMemory();
    $writer->setIndent(2);
    $writer->startElement('requests');
    $writer->writeAttribute('username', $this->credentials->getUsername());
    $writer->writeAttribute('password', $this->credentials->getPassword());
    $writer->writeAttribute('id', $this->credentials->getId());
    $writer->writeAttribute('token', $this->credentials->getToken());
    $writer->startElement('request');
    $writer->writeAttribute('service', $this->requestType);
    $this->requestBodyXml($writer);
    $writer->endElement();
    $writer->endElement();
    $writer->endDocument();
    return $writer->flush();
  }

  /**
   * Produce the request body.
   *
   * @param XMLWriter $writer
   */
  protected function requestBodyXml(\XMLWriter $writer) {}

  /**
   * Send the request to T-Insight.
   *
   * @return mixed
   */
  public function sendRequest() {
    $options = [
      CURLOPT_URL => $this->live ? TINSIGHT_LIVE_REQUEST_URL : TINSIGHT_TEST_REQUEST_URL,
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => $this->requestXml(),
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_SSL_VERIFYHOST =>  FALSE,
      CURLOPT_SSL_VERIFYPEER =>  FALSE,
    ];
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    $output = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    return $output;
  }

}
