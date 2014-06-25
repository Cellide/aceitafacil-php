<?php

namespace AceitaFacil\Tests\Integration;

use AceitaFacil\Client,
    AceitaFacil\Entity;

class CardTest extends \PHPUnit_Framework_TestCase
{   
    public function testSaveCard()
    {
        $client = new Client(true);
        $client->init($_ENV['APPID'], $_ENV['APPSECRET']);
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        
        $card = new Entity\Card();
        $card->number = "4111111111111111";
        $card->name = "John Doe";
        $card->exp_date = "205001";
        
        $response = $client->saveCard($customer, $card);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertEquals(1, count($cards), 'Objects were filled with one card');

        $found = $cards[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Card', $found, 'Object is a card');
        $this->assertNotEmpty($found->token, 'Token was received');
        $this->assertNotEmpty($found->brand, 'Brand was recognized');
        $this->assertNotEmpty($found->last_digits, 'Last digits were returned');
        $this->assertEquals(substr('4111111111111111', -4, 4), $found->last_digits, 'Last digits match');
        
        return $card;
    }

    /**
     * @depends testSaveCard
     */
    public function testGetAllCards($original_card)
    {
        $client = new Client(true);
        $client->init($_ENV['APPID'], $_ENV['APPSECRET']);
        
        $response = $client->getAllCards("1");
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertNotEmpty($cards, 'Objects were filled');
        
        $found_same_card = false;
        $card_token = null;
        foreach ($cards as $card) {
            $this->assertInstanceOf('AceitaFacil\Entity\Card', $card, 'Object is a card');
            $this->assertNotEmpty($card->token, 'Token was received');
            $this->assertNotEmpty($card->brand, 'Brand was recognized');
            $this->assertNotEmpty($card->last_digits, 'Last digits were returned');
            if (strpos($original_card->number, $card->last_digits)) {
                $found_same_card = true;
                $card_token = $card->token;
            }
        }
        $this->assertTrue($found_same_card, 'Original saved card was found on the list of returned cards');
        
        return $card_token;
    }

    /**
     * @depends testGetAllCards
     */
    public function testDeleteCard($card_token)
    {
        $client = new Client(true);
        $client->init($_ENV['APPID'], $_ENV['APPSECRET']);
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        
        $response = $client->deleteCard($customer, $card_token);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $cards = $response->getObjects();
        $this->assertNotEmpty($cards, 'Objects were filled');
        
        foreach ($cards as $card) {
            $this->assertInstanceOf('AceitaFacil\Entity\Card', $card, 'Object is a card');
            $this->assertNotEmpty($card->token, 'Token was received');
            $this->assertEquals($card_token, $card->token, 'Token received is the same passed');
            // API doesn't return other card info on removal, we won't test them here
        }
    }
}