<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class CardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSaveEmptyCard()
    {
        $client = new Client();
        $client->init('test', 'test');
        $card = new Entity\Card();
        $response = $client->saveCard($card);
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

        $card_number = '343434343434343';
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $card = new Entity\Card();
        $card->name = 'John Doe';
        $card->number = $card_number;
        $card->exp_date = '201705';
        
        $response = $client->saveCard($card);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertNotEmpty($cards, 'Objects were filled');
        
        foreach ($cards as $card) {
            $this->assertInstanceOf('AceitaFacil\Entity\Card', $card, 'Object is a card');
            $this->assertNotEmpty($card->token, 'Token was received');
            $this->assertNotEmpty($card->brand, 'Brand was recognized');
            $this->assertNotEmpty($card->last_digits, 'Last digits were returned');
            $this->assertEquals(substr($card_number, -4, 4), $card->last_digits, 'Last digits match');
        }
    }
    
    public function testGetAllCards()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"card":[{"token":"1234","card_brand":"amex","last_digits":"4343"},
                                      {"token":"5678","card_brand":"visa","last_digits":"7878"}]}'
                        );
            return $response;
        });

        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $response = $client->getAllCards();
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertNotEmpty($cards, 'Objects were filled');
        
        foreach ($cards as $card) {
            $this->assertInstanceOf('AceitaFacil\Entity\Card', $card, 'Object is a card');
            $this->assertNotEmpty($card->token, 'Token was received');
            $this->assertNotEmpty($card->brand, 'Brand was recognized');
            $this->assertNotEmpty($card->last_digits, 'Last digits were returned');
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteEmptyCard()
    {
        $client = new Client();
        $client->init('test', 'test');
        $card = new Entity\Card();
        $response = $client->deleteCard($card);
    }

    public function testDeleteCard()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"card":[{"token":"1234","status": "removed"}]}'
                        );
            return $response;
        });

        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $response = $client->deleteCard('1234');
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertNotEmpty($cards, 'Objects were filled');
        
        foreach ($cards as $card) {
            $this->assertInstanceOf('AceitaFacil\Entity\Card', $card, 'Object is a card');
            $this->assertNotEmpty($card->token, 'Token was received');
            // API doesn't return other card info on removal, we won't test them here
        }
    }
}