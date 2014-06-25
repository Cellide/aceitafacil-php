<?php

namespace AceitaFacil\Tests\Integration;

use AceitaFacil\Client;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvironmentVariables()
    {
        $this->assertNotEmpty(getenv('APPID'), 'App ID was set');
        $this->assertNotEmpty(getenv('APPSECRET'), 'App secret was set');
    }
}