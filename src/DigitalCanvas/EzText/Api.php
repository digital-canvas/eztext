<?php
namespace DigitalCanvas\EzText;

use DigitalCanvas\EzText\Request;
use DigitalCanvas\EzText\Response;
use DigitalCanvas\EzText\Adapter\HttpClientInterface;
use DigitalCanvas\EzText\Adapter\Curl as CurlAdapter;

/**
 * EzText API class
 *
 * @package EzText
 */
class Api {

  const VERSION = 'v1.0';

  const URL = 'https://app.eztexting.com/';

  /**
   * HTTP Client Adapter
   * @var HttpClientInterface $adapter
   */
  protected $adapter = null;

  /**
   * The url to send requests to
   * @var type
   */
  protected $url;

  protected $user;

  protected $password;


  const DELIVERY_EXPRESS = 1;
  const DELIVERY_STANDARD = 2;

  /**
   * Class constructor
   * @param string $user Your Ez Texting username
   * @param string $password Your Ez Texting password
   */
  public function __construct($user, $password) {
    $this->user = $user;
    $this->password = $password;
    $this->url = self::URL;
  }

  /**
   * Sets the HTTP Client Adapter
   *
   * If not provided Curl adapter will be used
   *
   * @param HttpClientInterface $adapter
   * @return Api
   */
  public function setHTTPClientAdapter(HttpClientInterface $adapter = null) {
    if (is_null($adapter)) {
      $adapter = new CurlAdapter();
    }
    $this->adapter = $adapter;
    return $this;
  }

  /**
   * Returns current HTTP Client Adapter
   *
   * @return HttpClientInterface
   */
  public function getHTTPClientAdapter() {
    if(is_null($this->adapter)){
      $this->setHTTPClientAdapter(null);
    }
    return $this->adapter;
  }

  /**
   * Sends a SMS text Message
   * @param string $message The message to send
   * @param array|string Phone number or array of phone numbers to send to.
   * @param array|string Group or array of groups to send to.
   * @param string $subject The message subject
   * @param int|DateTime The date/time to send the message Either a DateTime instace or a unix timestamp
   * @return array
   */
  public function sendMessage($message, $numbers = array(), $groups = array(), $subject = null, $schedule = null, $delivery = self::DELIVERY_EXPRESS) {
    if (!is_array($numbers)) {
        $numbers = array($numbers);
    }
    $numbers = array_filter($numbers, array($this, 'not_empty'));
    foreach ($numbers as $number) {
        if (!preg_match("/\\d{10}/", $number)) {
            throw new InvalidArgumentException("Phone number must be a 10 digit number");
        }
    }
    if (!is_array($groups)) {
        $groups = array($groups);
    }
    $groups = array_filter($groups, array($this, 'not_empty'));
    if(!$numbers && !$groups){
        throw new InvalidArgumentException("Must include either groups or numbers.");
    }
    $subject = trim($subject);
    if(mb_strlen($subject) > 13){
        throw new InvalidArgumentException("Subject must not be more than 13 characters.");
    }
    if($schedule instanceof DateTime){
        $schedule = $schedule->format('U');
    }
    if(!in_array($delivery, array(self::DELIVERY_EXPRESS, self::DELIVERY_STANDARD))){
        throw new InvalidArgumentException("Invalid delivery method.");
    }
    $params = array(
        'Message' => $message,
        'MessageTypeID' => $delivery
    );
    if($numbers){
        $params['PhoneNumbers'] = $numbers;
    }
    if($groups){
        $params['Groups'] = $groups;
    }
    if($subject){
        $params['Subject'] = $subject;
    }
    if($schedule){
        $params['StampToSend'] = $schedule;
    }
    return $this->sendRequest('sending/messages', $params, 'POST');
  }



  /**
   * Builds the Request object
   * @param type $path
   * @param array $params
   * @return Request
   */
  protected function buildRequest($path, array $params = array(), $method = "GET"){
    $uri = $this->url . $path;
    if ($method == 'GET') {
        $params['format'] = 'json';
    } else {
        $uri .= '?format=json';
    }
    $params['User'] = $this->user;
    $params['Password'] = $this->password;
    $request = new Request();
    $request->setUri($uri);
    $request->setMethod($method);
    $request->setParams($params);
    return $request;
  }

  /**
   * Sends request and returns response
   * @param string $path
   * @param array $params
   * @return Response
   */
  protected function sendRequest($path, array $params, $method = 'GET') {
    $request = $this->buildRequest($path, $params, $method);
    return $this->getHTTPClientAdapter()->sendRequest($request);

  }

  /**
   * Returns true if value is not empty
   *
   * @param mixed $value The value to check
   *
   * @return boolean True if not empty, false if empty
   */
  public function not_empty($value) {
    return !empty($value);
  }

}
