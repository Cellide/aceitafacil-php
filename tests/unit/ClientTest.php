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
        $card = $client->saveCard('John Doe', '1111111111111111', '111', '201705');
    }
    
    public function testInit()
    {
        $client = new Client();
        $client->init('test', 'test');
        $this->assertInstanceOf('AceitaFacil\Client', $client, 'Client appears to be initiated correctly');
        return $client;
    }
    
    public function testDetectResponseError()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(400, array(),
                            '{"errors":[{"message":"error1.\nerror2.","name":"INVALID PARAMETERS","at":""}]}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $card = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        
        $this->assertInstanceOf('AceitaFacil\ResponseError', $card, 'Received an error');
        $this->assertTrue($card->isError(), 'Is an error');
        $this->assertTrue(($card->getHttpStatus() >= 400), 'Http status above 400');
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSaveCardNoArguments()
    {
        $client = new Client();
        $client->init('test', 'test');
        $card = $client->saveCard(null, null, null, null);
    }
    
    public function testSaveCard()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"card":[{"token":"1234","card_brand":"amex","last_digits":"4343"}]}'
                        );
            return $response;
        });

        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $card = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        
        $this->assertInstanceOf('AceitaFacil\ResponseCard', $card, 'Card was saved');
        $this->assertFalse($card->isError(), 'Not an error');
        $this->assertNotEmpty($card->getToken(), 'Token was received');
        $this->assertNotEmpty($card->getBrand(), 'Brand was recognized');
        $this->assertNotEmpty($card->getLastDigits(), 'Last digits were returned');
        $this->assertEquals(substr('343434343434343', -4, 4), $card->getLastDigits(), 'Last digits match');
    }

    
}