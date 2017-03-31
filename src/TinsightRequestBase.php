<?php

namespace Czigor\Tinsight;

use Monolog\Logger;

define('TINSIGHT_TEST_REQUEST_URL', 'https://staging.sgiws.com/api/gateway.cfc');
define('TINSIGHT_LIVE_REQUEST_URL', 'https://sgiws.com/api/gateway.cfc');

abstract class TinsightRequestBase {

  /**
   * Whether to use the live or test url.
   *
   * Defaults to FALSE.
   *
   * @var bool
   */
  protected $live = FALSE;

  /**
   * The credentials object.
   *
   * @var TinsightCredentialsInterface
   */
  protected $credentials;

  /**
   * The request type.
   *
   * Currently only 'rate is supported.
   *
   * @var string.
   */

  protected $requestType;

  /**
   * The logger service used to log.
   *
   * @var Logger
   */
  protected $logger;

  /**
   * Whether to actually send the request.
   *
   * Used for debugging with $logger, when we only want to analyse the created
   * request xml.
   *
   * @var bool
   */
  protected $sendRequest = TRUE;


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
    $xml = $this->requestXml();
    if ($this->logger) {
      $this->logger->info($xml, ['tinsight' => $this->requestType . ' request']);
    }

    if ($this->sendRequest) {
      $options = [
        CURLOPT_URL => $this->live ? TINSIGHT_LIVE_REQUEST_URL : TINSIGHT_TEST_REQUEST_URL,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $xml,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYHOST =>  FALSE,
        CURLOPT_SSL_VERIFYPEER =>  FALSE,
      ];
      $ch = curl_init();
      curl_setopt_array($ch, $options);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
      $output = curl_exec($ch);
      $error = curl_error($ch);
      if ($this->logger) {
        $this->logger->info($output, ['tinsight' => $this->requestType . ' response']);
        $this->logger->info($error, ['tinsight' => $this->requestType . ' error response']);
      }
      curl_close($ch);

      return $output;
    }
  }

  /**
   * Setter for logger.
   */
  public function setLogger(Logger $logger) {
    $this->logger = $logger;
  }

  /**
   * Setter for sendRequest.
   */
  public function setSendRequest($send_request) {
    $this->sendRequest = $send_request;
  }

}
