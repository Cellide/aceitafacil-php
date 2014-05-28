<?php

namespace AceitaFacil\Tests;

use AceitaFacil\Client;

/**
 * @covers AceitaFacil\Client;
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $client = new Client();
        $this->assertInstanceOf('AceitaFacil\Client', $client, 'Client was instantiated correctly');
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testNoInit()
    {
        $client = new Client();
        $card = $client->saveCard('John Doe', '1111111111111111', '111', '201705');
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
}