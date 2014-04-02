<?php
namespace DigitalCanvas\EzText\Adapter;

use DigitalCanvas\EzText\Request;
use DigitalCanvas\EzText\Response;
use DigitalCanvas\EzText\Exception\RuntimeException;
use DigitalCanvas\EzText\Exception\UnexpectedValueException;
use HTTP_Request2;
use HTTP_Request2_Response;

/**
 * Sends Requests using the HTTP_Request2 from PEAR
 *
 * @package EzText
 * @category Adapter
 * @see http://pear.php.net/package/HTTP_Request2
 */
class PEAR implements HttpClientInterface {

  /**
   * HTTP Client instance
   * @var HTTP_Request2 $client
   */
  public $client;

  /**
   *
   * @var HTTP_Request2_Response $response
   */
  public $response;

  /**
   * Class constructor
   *
   * Sets HTTP Client instance
   *
   * @param HTTP_Request2 $client
   */
  public function __construct(HTTP_Request2 $client) {
    $this->setClient($client);
  }

  /**
   * Sets HTTP Client instance
   *
   * @param HTTP_Request2 $client
   */
  public function setClient(HTTP_Request2 $client) {
    $this->client = $client;
  }

  /**
   * Returns the http client
   * @return HTTP_Request2
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * Returns the PEAR response object
   * @return HTTP_Request2_Response
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
    $this->client->setUrl($request->getUri());
    if($request->getMethod() == "GET"){
      $this->client->getUrl()->setQueryVariables($request->getParams());
    }else{
      $this->client->addPostParameter($request->getParams());
    }
    $this->client->setConfig('ssl_verify_peer', true);
    $this->client->setConfig('ssl_verify_host', true);
    $this->client->setConfig('ssl_cafile', __DIR__ . DIRECTORY_SEPARATOR . "cacert.pem");
    $this->client->setMethod($request->getMethod());
    $this->client->setHeader('Accept', 'application/json');
    $this->response = $this->client->send();
    if ($this->response->getHeader('Content-Type') != 'application/json') {
      throw new UnexpectedValueException("Unknown response format.");
    }
    $body = json_decode($this->response->getBody(), true);
    $response = new Response();
    $response->setRawResponse($this->response->getBody());
    $response->setBody($body);
    $response->setHeaders($this->response->getHeader());
    $response->setStatus($this->response->getReasonPhrase(), $this->response->getStatus());
    return $response;
  }
}
