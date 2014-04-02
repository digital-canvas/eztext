<?php
namespace DigitalCanvas\EzText\Adapter;

use DigitalCanvas\EzText\Request;
use DigitalCanvas\EzText\Response;
use DigitalCanvas\EzText\Exception\RuntimeException;
use DigitalCanvas\EzText\Exception\UnexpectedValueException;

/**
 * Sends Requests using the Guzzle\Http\Client
 *
 * @package EzText
 * @category Adapter
 * @see http://guzzlephp.org/
 */
class Guzzle implements HttpClientInterface {

  /**
   * HTTP Client instance
   * @var \Guzzle\Http\Client $client
   */
  public $client;

  /**
   *
   * @var Zend_Http_Response $response
   */
  public $response;

  /**
   * Class constructor
   *
   * Sets HTTP Client instance
   *
   * @param \Guzzle\Http\Client $client
   */
  public function __construct(\Guzzle\Http\Client $client) {
    $this->setClient($client);
  }

  /**
   * Sets HTTP Client instance
   *
   * @param \Guzzle\Http\Client $client
   */
  public function setClient(\Guzzle\Http\Client $client) {
    $this->client = $client;
  }

  /**
   * Returns the http client
   * @return \Guzzle\Http\Client
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * Returns the response object
   * @return Response
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
    $url = \Guzzle\Http\Url::factory($request->getUri());
    if($request->getMethod() == 'GET'){
      $url->setQuery($request->getParams());
    }
    $this->client->setBaseUrl($url);
    $grequest = $this->client->createRequest($request->getMethod(), $url);
    if ($request->getMethod() != 'GET') {
      $grequest->addPostFields($request->getParams());
    }
    $grequest->addHeader('Accept', 'application/json');
    $this->response = $this->client->send($grequest);
    if (!$this->response->isContentType('application/json')) {
      throw new UnexpectedValueException("Unknown response format.");
    }
    $response = new Response();
    $response->setRawResponse($this->response->__toString());
    $response->setBody($this->response->json());
    $response->setHeaders($this->response->getHeaders()->toArray());
    $response->setStatus($this->response->getReasonPhrase(), $this->response->getStatusCode());
    return $response;
  }
}
