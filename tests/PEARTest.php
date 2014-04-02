<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

class PEARTest extends PHPUnit_Framework_TestCase {

    public $config;

    public function setUp() {
        $this->config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
    }

    public function testSendMessage(){
        $message = 'This is a test message.';
        $number = $this->config['number'];
        $adapter = new DigitalCanvas\EzText\Adapter\PEAR(new HTTP_Request2());
        $sms = new DigitalCanvas\EzText\Api($this->config['User'], $this->config['Password']);
        $sms->setHTTPClientAdapter($adapter);
        $response = $sms->sendMessage($message, $number);
        $this->assertEquals($response->getStatusCode(), 201, "Status code does not match");
    }
}
