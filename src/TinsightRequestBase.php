<?php

namespace Czigor\Tinsight;

define('TINSIGHT_TEST_REQUEST_URL', 'http(s)://staging.sgiws.com/api/gateway.cfc');
define('TINSIGHT_LIVE_REQUEST_URL', 'http(s)://sgiws.com/api/gateway.cfc');

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
    $writer = new XMLWriter();
    $writer->openMemory();
    $writer->setIndent(2);
    $writer->startElement('requests');
    if (!empty($this->credentials->username)) {
      $writer->writeAttribute('username', $this->credentials->username);
    }
    if (!empty($this->credentials->password)) {
      $writer->writeAttribute('password', $this->credentials->password);
    }
    if (!empty($this->credentials->id)) {
      $writer->writeAttribute('id', $this->credentials->id);
    }
    if (!empty($this->credentials->token)) {
      $writer->writeAttribute('token', $this->credentials->token);
    }
    $writer->startElement('request');
    $writer->writeAttribute('service', $this->requestType);
    $this->requestBody($writer);
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
  protected function requestBodyXml(XMLWriter $writer) {}

  /**
   * Send the request to T-Insight.
   *
   * @return mixed
   */
  public function sendRequest() {
    $options = [
      CURLOPT_URL => $this->live ? TINSIGHT_LIVE_REQUEST_URL : TINSIGHT_TEST_REQUEST_URL,
      CURLOPT_POST => TRUE,
      CURLOPT_POSTFIELDS => $this->requestXml(),
      CURLOPT_RETURNTRANSFER => TRUE,
    ];
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
  }

}
