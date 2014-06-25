<?php

namespace AceitaFacil\Tests\Integration;

use AceitaFacil\Client;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvironmentVariables()
    {
        $this->assertNotEmpty($_ENV['APPID'], 'App ID was set');
        $this->assertNotEmpty($_ENV['APPSECRET'], 'App secret was set');
    }
}