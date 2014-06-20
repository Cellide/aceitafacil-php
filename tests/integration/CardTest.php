<?php

namespace AceitaFacil\Tests;

use AceitaFacil\Client,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class CardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSaveCardNoArguments()
    {
        $client = new Client();
        $client->init('test', 'test');
        $response = $client->saveCard(null, null, null, null);
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
        $response = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertEquals(1, count($cards), 'Objects were filled with one card');

        $card = $card[0];
        $this->assertInstanceOf('AceitaFacil\Card', $card, 'Object is a card');
        $this->assertNotEmpty($card->getToken(), 'Token was received');
        $this->assertNotEmpty($card->getBrand(), 'Brand was recognized');
        $this->assertNotEmpty($card->getLastDigits(), 'Last digits were returned');
        $this->assertEquals(substr('343434343434343', -4, 4), $card->getLastDigits(), 'Last digits match');
    }
}