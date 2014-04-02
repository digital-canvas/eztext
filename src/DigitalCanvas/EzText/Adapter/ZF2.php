<?php
namespace DigitalCanvas\EzText\Adapter;

use DigitalCanvas\EzText\Request;
use DigitalCanvas\EzText\Response;
use DigitalCanvas\EzText\Exception\RuntimeException;
use DigitalCanvas\EzText\Exception\UnexpectedValueException;

/**
 * Sends Requests using the Zend\Http\Client library from Zend Framework 2.x
 *
 * @package EzText
 * @category Adapter
 * @see http://framework.zend.com/manual/2.2/en/modules/zend.http.client.html
 */
class ZF2 implements HttpClientInterface {

  /**
   * HTTP Client instance
   * @var \Zend\Http\Client $client
   */
  public $client;

  /**
   *
   * @var Zend\Http\Response $response
   */
  public $response;

  /**
   * Class constructor
   *
   * Sets HTTP Client instance
   *
   * @param \Zend\Http\Client $client
   */
  public function __construct(\Zend\Http\Client $client) {
    $this->setClient($client);
  }

  /**
   * Sets HTTP Client instance
   *
   * @param \Zend\Http\Client $client
   */
  public function setClient(\Zend\Http\Client $client) {
    $this->client = $client;
  }

  /**
   * Returns the zend http client
   * @return \Zend\Http\Client
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * Returns the zend response object
   * @return \Zend\Http\Response
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
    $this->client->setOptions(array(
      'adapter' => 'Zend\Http\Client\Adapter\Curl',
      'curloptions' => array(
        CURLOPT_CAINFO => __DIR__ . DIRECTORY_SEPARATOR . "cacert.pem",
        CURLOPT_SSL_VERIFYHOST => true,
        CURLOPT_SSL_VERIFYPEER => true
      )
    ));
    $this->client->setMethod($request->getMethod());
    $this->client->setHeaders(array('Accept' => 'application/json'));
    $this->response = $this->client->send();
    if ($this->response->getHeaders()->get('Content-Type')->getFieldValue() != 'application/json') {
      throw new UnexpectedValueException("Unknown response format.");
    }
    $body = json_decode($this->response->getContent(), true);
    $response = new Response();
    $response->setRawResponse($this->response->toString());
    $response->setBody($body);
    $response->setHeaders($this->response->getHeaders()->toArray());
    $response->setStatus($this->response->getReasonPhrase(), $this->response->getStatusCode());
    return $response;
  }
}
