<?php
namespace DigitalCanvas\EzText\Adapter;

use DigitalCanvas\EzText\Request;
use DigitalCanvas\EzText\Response;
use DigitalCanvas\EzText\Exception\RuntimeException;
use DigitalCanvas\EzText\Exception\UnexpectedValueException;

/**
 * Sends Requests using the Zend_Http_Client library from Zend Framework 1.x
 *
 * @package EzText
 * @category Adapter
 * @see http://framework.zend.com/manual/1.12/en/zend.http.client.html
 */
class Zend implements HttpClientInterface {

  /**
   * HTTP Client instance
   * @var \Zend_Http_Client $client
   */
  public $client;

  /**
   *
   * @var \Zend_Http_Response $response
   */
  public $response;

  /**
   * Class constructor
   *
   * Sets HTTP Client instance
   *
   * @param \Zend_Http_Client $client
   */
  public function __construct(\Zend_Http_Client $client) {
    $this->setClient($client);
  }

  /**
   * Sets HTTP Client instance
   *
   * @param \Zend_Http_Client $client
   */
  public function setClient(\Zend_Http_Client $client) {
    $this->client = $client;
  }

  /**
   * Returns the zend http client
   * @return \Zend_Http_Client
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * Returns the zend response object
   * @return \Zend_Http_Response
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * Sends a request and returns a response
   *
   * @param Request $request
   * @return Response
   */
  public function sendRequest(Request $request) {
    $this->client->setUri($request->getUri());
    if ($request->getMethod() == 'GET') {
      $this->client->setParameterGet($request->getParams());
    } else {
      $this->client->setParameterPost($request->getParams());
    }
    $this->client->setMethod($request->getMethod());
    $this->client->setConfig(array(
      'adapter' => 'Zend_Http_Client_Adapter_Curl',
      'curloptions' => array(
        CURLOPT_CAINFO => __DIR__ . DIRECTORY_SEPARATOR . "cacert.pem",
        CURLOPT_SSL_VERIFYHOST => true,
        CURLOPT_SSL_VERIFYPEER => true
      )
    ));
    $this->client->setHeaders('Accept', 'application/json');
    $this->response = $this->client->request();
    if ($this->response->getHeader('Content-Type') != 'application/json') {
      throw new UnexpectedValueException("Unknown response format.");
    }
    $body = json_decode($this->response->getBody(), true);
    $response = new Response();
    $response->setRawResponse($this->response->asString());
    $response->setBody($body);
    $response->setHeaders($this->response->getHeaders());
    $response->setStatus($this->response->getMessage(), $this->response->getStatus());
    return $response;
  }

}
