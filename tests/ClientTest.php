<?php

namespace AceitaFacil\Tests;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $client = new \AceitaFacil\Client();
        $this->assertInstanceOf('AceitaFacil\Client', $client, 'Client was instantiated correctly');
    }
}