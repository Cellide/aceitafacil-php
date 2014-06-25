<?php

namespace AceitaFacil\Tests\Integration;

use AceitaFacil\Client,
    AceitaFacil\Entity;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testDetectResponseErrorUnauthorized()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        $response = $client->getAllCards("1");
        
        $this->assertInstanceOf('AceitaFacil\Response', $response, 'Received a response');
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(401, $response->getHttpStatus(), 'Http status 401');
        
        $objects = $response->getObjects();
        $this->assertNotEmpty($objects, 'Parsed entities available');
        foreach ($objects as $object) {
            $this->assertInstanceOf('AceitaFacil\Entity\Error', $object, 'Parsed object is an Error');
        }
    }
    
    public function testDetectResponseSuccess()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        $response = $client->getVendor(getenv('APPID'));
        
        $this->assertInstanceOf('AceitaFacil\Response', $response, 'Received a response');
        $this->assertFalse($response->isError(), 'Is not an error');
        $this->assertTrue(($response->getHttpStatus() < 300), 'Http status < 300');
        
        $objects = $response->getObjects();
        $this->assertNotEmpty($objects, 'Parsed entities available');
        foreach ($objects as $object) {
            $this->assertNotInstanceOf('AceitaFacil\Entity\Error', $object, 'Parsed object is not an Error');
        }
    }
}