<?php
namespace DigitalCanvas\EzText;

/**
 * HTTP Request Object
 *
 * @package EzText
 * @category Request
 */
class Request {

  const GET = 'GET';
  const POST = 'POST';
  const PUT = 'PUT';
  const DELETE = 'DELETE';

  /**
   * The uri to make the request to
   * @var string  $uri
   */
  protected $uri;

  /**
   * The HTTP method to use for the request
   * @var string $method
   */
  protected $method = 'GET';

  /**
   * Array of parameters to pass to the request
   * @var array $params
   */
  protected $params = array();

  /**
   * Sets the content type
   * @var string $content_type
   */
  protected $content_type = 'application/json';


  /**
   * Sets the uri of the request
   *
   * @param string $uri
   * @return Request
   */
  public function setUri($uri){
    $this->uri = $uri;
    return $this;
  }

  /**
   * Returns uri for the request
   *
   * @return string
   */
  public function getUri(){
    return $this->uri;
  }

  /**
   * Sets the HTTP method of the request
   *
   * @param string $method
   * @return Request
   */
  public function setMethod($method = Request::GET){
    $this->method = $method;
    return $this;
  }

  /**
   * Returns HTTP method of the request
   *
   * @return string
   */
  public function getMethod(){
    return $this->method;
  }

  /**
   * Sets params to send with request
   * @param array $params
   * @return Request
   */
  public function setParams(array $params = array()){
    $this->params = $params;
    return $this;
  }

  /**
   * Returns array of params for the request
   * @return array
   */
  public function getParams(){
    return $this->params;
  }

}
