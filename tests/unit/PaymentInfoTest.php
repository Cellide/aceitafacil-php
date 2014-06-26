<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class PaymentInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetItemInfoInvalidParameters()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $client->getPaymentItemInfo('', '');
    }
    
    public function testGetItemInfo()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"item":{
                                "id":"item_1234",
                                "vendor_id":"123",
                                "vendor_name":"Acme",
                                "provider_id":"123",
                                "provider_name":"Acme",
                                "amount":500,
                                "description":"Razor blade",
                                "trigger_lock":false}
                            }'
                        );
            return $response;
        });

        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $payment_id = '1234';
        $item_id = 'item_1234';
        
        $response = $client->getPaymentItemInfo($payment_id, $item_id);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $items = $response->getObjects();
        $this->assertNotEmpty($items, 'Objects were filled');
        $this->assertEquals(1, count($items), 'Exactly one item found');
        
        $item = $items[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $item, 'Object is an Item');
        $this->assertNotEmpty($item->id, 'Item ID found');
        $this->assertNotEmpty($item->description, 'Item description found');
        $this->assertNotEmpty($item->amount, 'Item amount found');
        $this->assertTrue(is_numeric($item->amount), 'Item amount is a value');
        $this->assertEmpty($item->fee_split, 'Item fee split is not returned in this method');
        $this->assertTrue(is_bool($item->trigger_lock), 'Item trigger_lock is a boolean');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateItemInfoNoPaymentID()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $payment_id = '';
        $updated = new Entity\Item();
        $updated->id = "item_1234";
        $updated->trigger_lock = true;
        $updated->vendor = new Entity\Vendor();
        $updated->vendor->id = '123';
        
        $response = $client->updatePaymentItemInfo($payment_id, $updated);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateItemMissingInfo()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $payment_id = '1234';
        $updated = new Entity\Item();
        
        $response = $client->updatePaymentItemInfo($payment_id, $updated);
    }

    public function testUpdateItemInfo()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"item":{
                                "id":"item_1234",
                                "vendor_id":"123",
                                "vendor_name":"Acme",
                                "provider_id":"123",
                                "provider_name":"Acme",
                                "amount":500,
                                "description":"Razor blade",
                                "trigger_lock":true}
                            }'
                        );
            return $response;
        });

        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $payment_id = '1234';
        $updated = new Entity\Item();
        $updated->id = "item_1234";
        $updated->trigger_lock = true;
        $updated->vendor = new Entity\Vendor();
        $updated->vendor->id = '123';
        
        $response = $client->updatePaymentItemInfo($payment_id, $updated);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $items = $response->getObjects();
        $this->assertNotEmpty($items, 'Objects were filled');
        $this->assertEquals(1, count($items), 'Exactly one item found');
        
        $item = $items[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $item, 'Object is an Item');
        $this->assertNotEmpty($item->id, 'Item ID found');
        $this->assertNotEmpty($item->description, 'Item description found');
        $this->assertNotEmpty($item->amount, 'Item amount found');
        $this->assertTrue(is_numeric($item->amount), 'Item amount is a value');
        $this->assertEmpty($item->fee_split, 'Item fee split is not returned in this method');
        $this->assertEquals($updated->trigger_lock, $item->trigger_lock, 'Item trigger_lock was set to true');
    }
}