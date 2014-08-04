<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $client = new Client();
        $this->assertInstanceOf('AceitaFacil\Client', $client, 'Client instantiated correctly');
        return $client;
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testNoInit()
    {
        $client = new Client();
        $response = $client->getAllCards("1");
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidInit()
    {
        $client = new Client();
        $client->init('', null);
    }
    
    public function testInit()
    {
        $client = new Client();
        $client->init('test', 'test');
        $this->assertInstanceOf('AceitaFacil\Client', $client, 'Client appears to be initiated correctly');
    }
    
    public function testSetPushEndpoint()
    {
        $client = new Client();
        $client->setPushEndpoint('https://acme.com');
    }
}