<?php

namespace AceitaFacil\Tests;

use AceitaFacil\Client,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class ResponseTest extends \PHPUnit_Framework_TestCase
{
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
        $response = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        
        $this->assertInstanceOf('AceitaFacil\Response', $response, 'Received a response');
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertTrue(($response->getHttpStatus() >= 400), 'Http status >= 400');
        $this->assertNotEmpty($response->getErrors(), 'Errors were found');
        $this->assertEmpty($response->getObjects(), 'No parsed entities available');
    }
    
    public function testDetectResponseErrorNoBody()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(401, array(),
                            'Unauthorized error 401'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $response = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        
        $this->assertInstanceOf('AceitaFacil\Response', $response, 'Received a response');
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertTrue(($response->getHttpStatus() >= 400), 'Http status >= 400');
        $this->assertNotEmpty($response->getErrors(), 'Errors were found');
        $this->assertEmpty($response->getObjects(), 'No parsed entities available');
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testDetectResponseErrorBodyNotRecognized()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"unknown":{}}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $response = $client->saveCard('John Doe', '343434343434343', '111', '201705');
    }
    
    public function testDetectResponseSuccess()
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
        $response = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        
        $this->assertInstanceOf('AceitaFacil\Response', $response, 'Received a response');
        $this->assertFalse($response->isError(), 'Is not an error');
        $this->assertTrue(($response->getHttpStatus() < 300), 'Http status < 300');
        $this->assertEmpty($response->getErrors(), 'Errors were not found');
        $this->assertNotEmpty($response->getObjects(), 'Parsed entities are available');
    }
}