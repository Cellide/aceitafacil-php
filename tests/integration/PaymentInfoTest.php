<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class PaymentInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testBeginByMakingPayment()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
 
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = getenv('APPID');
        $vendor->name = 'Acme';
        
        // making a payment
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 9;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = 'Random purchase';
        $total_amount = $item1->amount;
        
        $response = $client->makePayment($customer, $description, $total_amount, $items);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $payments = $response->getObjects();
        $payment = $payments[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'Payment is ok');
        $this->assertNotEmpty($payment->id, 'Transaction ID found');
        $this->assertNotEmpty($payment->items, 'Items were found');
        
        return $payment;
    }
    
    /**
     * @depends testBeginByMakingPayment
     */
    public function testGetItemInfo($original_payment)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'),getenv('APPSECRET'));
        
        $payment_id = $original_payment->id;
        $item = $original_payment->items[0];
        
        $response = $client->getPaymentItemInfo($payment_id, $item->id);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $items = $response->getObjects();
        $this->assertNotEmpty($items, 'Objects were filled');
        $this->assertEquals(1, count($items), 'Exactly one item found');
        
        $found = $items[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $found, 'Object is an Item');
        $this->assertNotEmpty($found->id, 'Item ID found');
        $this->assertNotEmpty($found->description, 'Item description found');
        $this->assertNotEmpty($found->amount, 'Item amount found');
        $this->assertTrue(is_numeric($found->amount), 'Item amount is a value');
        $this->assertEmpty($found->fee_split, 'Item fee split is not returned in this method');
        $this->assertTrue(is_bool($found->trigger_lock), 'Item trigger_lock is a boolean');
        $this->assertEquals($item->trigger_lock, $found->trigger_lock, 'Item trigger_lock matches the desired change');
        
        return $original_payment;
    }

    /**
     * @depends testGetItemInfo
     */
    public function testUpdateItemInfo($original_payment)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'),getenv('APPSECRET'));
        
        $payment_id = $original_payment->id;
        $original_item = $original_payment->items[0];
        
         // let's change the trigger_lock status
        $item = $original_item;
        $item->trigger_lock = !$original_item->trigger_lock;
        
        $response = $client->updatePaymentItemInfo($payment_id, $item);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $items = $response->getObjects();
        $this->assertNotEmpty($items, 'Objects were filled');
        $this->assertEquals(1, count($items), 'Exactly one item found');
        
        $found = $items[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $found, 'Object is an Item');
        $this->assertNotEmpty($found->id, 'Item ID found');
        $this->assertNotEmpty($found->description, 'Item description found');
        $this->assertNotEmpty($found->amount, 'Item amount found');
        $this->assertTrue(is_numeric($found->amount), 'Item amount is a value');
        $this->assertEmpty($found->fee_split, 'Item fee split is not returned in this method');
        $this->assertTrue(is_bool($found->trigger_lock), 'Item trigger_lock is a boolean');
        $this->assertEquals($item->trigger_lock, $found->trigger_lock, 'Item trigger_lock matches the desired change');
    }

    public function testGetInexistentItemInfo()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'),getenv('APPSECRET'));
        
        $response = $client->getPaymentItemInfo('asdfasdf', 'asdfasdf');
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(404, $response->getHttpStatus(), 'HTTP Status 404 returned');
        
        $objects = $response->getObjects();
        $this->assertNotEmpty($objects, 'Parsed entities available');
        foreach ($objects as $object) {
            $this->assertInstanceOf('AceitaFacil\Entity\Error', $object, 'Parsed object is an Error');
        }
    }
    
    public function testUpdateInexistentItemInfo()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'),getenv('APPSECRET'));
        
        $response = $client->getPaymentItemInfo('asdfasdf', 'asdfasdf');
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(404, $response->getHttpStatus(), 'HTTP Status 404 returned');
        
        $objects = $response->getObjects();
        $this->assertNotEmpty($objects, 'Parsed entities available');
        foreach ($objects as $object) {
            $this->assertInstanceOf('AceitaFacil\Entity\Error', $object, 'Parsed object is an Error');
        }
    }
}