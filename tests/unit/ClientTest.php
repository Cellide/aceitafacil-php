<?php

namespace AceitaFacil\Tests;

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
        $response = $client->saveCard('John Doe', '1111111111111111', '111', '201705');
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
}