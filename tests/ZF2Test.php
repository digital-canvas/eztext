<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

class ZF2Test extends PHPUnit_Framework_TestCase {

    public $config;

    public function setUp() {
        $this->config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
    }

    public function testSendMessage(){
        $message = 'This is a test message.';
        $number = $this->config['number'];
        $client = new Zend\Http\Client();
        $adapter = new DigitalCanvas\EzText\Adapter\ZF2($client);
        $sms = new DigitalCanvas\EzText\Api($this->config['User'], $this->config['Password']);
        $sms->setHTTPClientAdapter($adapter);
        $response = $sms->sendMessage($message, $number);
        $this->assertEquals($response->getStatusCode(), 201, "Status code does not match");
    }
}
