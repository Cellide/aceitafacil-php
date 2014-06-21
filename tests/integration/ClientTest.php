<?php

namespace AceitaFacil\Tests\Integration;

use AceitaFacil\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvironmentVariables()
    {
        $this->assertNotEmpty($_ENV['APPID'], 'App ID was set');
        $this->assertNotEmpty($_ENV['APPSECRET'], 'App secret was set');
    }
    
    public function testSaveCard()
    {
        $client = new Client(true);
        $client->init($_ENV['APPID'], $_ENV['APPSECRET']);
        $card = $client->saveCard('John Doe', '343434343434343', '111', '201705');
        
        $this->assertInstanceOf('AceitaFacil\ResponseCard', $card, 'Card was saved');
        $this->assertFalse($card->isError(), 'Not an error');
        $this->assertNotEmpty($card->getToken(), 'Token was received');
        $this->assertNotEmpty($card->getBrand(), 'Brand was recognized');
        $this->assertNotEmpty($card->getLastDigits(), 'Last digits were returned');
        $this->assertEquals(substr('343434343434343', -4, 4), $card->getLastDigits(), 'Last digits match');
    }
}