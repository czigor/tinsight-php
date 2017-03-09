<?php

namespace Czigor\Tinsight;

define('TINSIGHT_TEST_REQUEST_URL', 'http(s)://staging.sgiws.com/api/gateway.cfc');
define('TINSIGHT_LIVE_REQUEST_URL', 'http(s)://sgiws.com/api/gateway.cfc');

abstract class TinsightRequestBase {

  protected $live = FALSE;

  protected $username = '';

  protected $password = '';

  protected $id = '';

  protected $token = '';

  protected $requestType = 'rate';

  public function __construct($live = FALSE, $credentials = []) {
    $this->live = $live;
    $this->username = empty($credentials['username']) ? '' : $credentials['username'];
    $this->password = empty($credentials['password']) ? '' : $credentials['password'];
    $this->id = empty($credentials['id']) ? '' : $credentials['id'];
    $this->token = empty($credentials['token']) ? '' : $credentials['token'];
    $this->requestType = $request_type;
  }

  public function requestXml() {
    $writer = new XMLWriter();
    $writer->openMemory();
    $writer->setIndent(2);
    $writer->startElement('requests');
    if (!empty($this->username)) {
      $writer->writeAttribute('username', $this->username);
    }
    if (!empty($this->password)) {
      $writer->writeAttribute('password', $this->password);
    }
    if (!empty($this->id)) {
      $writer->writeAttribute('id', $this->id);
    }
    if (!empty($this->token0)) {
      $writer->writeAttribute('token', $this->token);
    }
    $writer->startElement('request');
    $writer->writeAttribute('service', $this->requestType);
    $this->requestBody($writer);
    $writer->endElement();
    $writer->endElement();
    $writer->endDocument();
    return $writer->flush();
  }

  protected function requestBodyXml(XMLWriter $writer) {}

  protected function sendRequest() {
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
  }

}
