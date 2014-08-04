<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMakePaymentNoCardInfo()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $card = new Entity\Card();
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 5;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = 'Random purchase';
        
        $response = $client->makePayment($customer, $items, $description, $card);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMakePaymentNoCustomerInfo()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $customer = new Entity\Customer();
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 5;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = 'Random purchase';
        
        $response = $client->makePayment($customer, $items, $description, $card);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMakePaymentNoDescription()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 5;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = '';
        
        $response = $client->makePayment($customer, $items, $description, $card);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMakePaymentTotalAmountEqualsZero()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 0;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = 'random purchase';
        
        $response = $client->makePayment($customer, $items, $description, $card);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMakePaymentNoItems()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $description = 'random purchase';
        
        $response = $client->makePayment($customer, array(), $description);
    }
    
    public function testMakeCardPayment()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"id":"ab123456",
                              "organization_id":"1234",
                              "organization_name":"Acme",
                              "customer_id":"1",
                              "customer_name":"John Doe",
                              "description":"random purchase",
                              "customer_email":"johndoe@acme.com",
                              "chargetype":"AVISTA",
                              "paymentmethod":"CREDITCARD",
                              "payer":"CUSTOMER",
                              "attempt_count":1,
                              "attempted":true,
                              "closed":false,
                              "paid":true,
                              "period_start":"2014-06-24 00:00:00",
                              "period_end":"2014-06-24 00:00:00",
                              "total_amount":500,
                              "next_charge_attempt":"2014-06-25 00:00:00",
                              "items":[
                                    {"id":"item_1",
                                     "vendor_id":"1234",
                                     "vendor_name":"Acme",
                                     "provider_id":"1234",
                                     "provider_name":"Acme",
                                     "amount":456,
                                     "description":"Item 1",
                                     "trigger_lock":false },
                                    {"id":"item_2",
                                    "vendor_id":"aaaa",
                                    "vendor_name":"aceitaFacil",
                                    "provider_id":"aaaa",
                                    "provider_name":"aceitaFacil",
                                    "amount":44,
                                    "description":"Tarifa aceitaFacil",
                                    "trigger_lock":false}
                                    ]
                              }'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 5;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = 'Random purchase';
        
        $response = $client->makePayment($customer, $items, $description, $card);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $payments = $response->getObjects();
        $this->assertNotEmpty($payments, 'Objects were filled');
        
        $payment = $payments[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'Payment is ok');
        $this->assertNotEmpty($payment->id, 'Transaction ID found');
        $this->assertNotEmpty($payment->description, 'Description found');
        $this->assertNotEmpty($payment->charge_type, 'Charge type found');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertGreaterThanOrEqual(0, $payment->attempt_count, 'Attempts count ok');
        $this->assertTrue($payment->paid, 'Actual payment was made');
        $this->assertInstanceOf('DateTime', $payment->period_start, 'Start period parsed');
        $this->assertInstanceOf('DateTime', $payment->period_end, 'End period parsed');
        $this->assertInstanceOf('DateTime', $payment->next_charge_attempt, 'Next charge attempt parsed');
        
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $this->assertNotEmpty($payment->items, 'Items were found');
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $payment->items[0], 'Items array consist of Item objects');
        
        $this->assertEquals(array_reduce($items, function ($sum, $item) {return $sum+$item->amount; }), $payment->total_amount, 'Total amount found matches items');
    }

    public function testUnauthorizedPayment()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(402, array(),
                            '{"errors":[{"message":"N&atilde;o autorizada","name":"CHARGE ERROR","at":""}]}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
        $items = array();
        $item1 = new Entity\Item();
        $item1->id = 10;
        $item1->description = 'Razor blade';
        $item1->amount = 5;
        $item1->vendor = $vendor;
        $item1->fee_split = 1;
        $item1->trigger_lock = false;
        $items[] = $item1;
        
        $description = 'Random purchase';
        
        $response = $client->makePayment($customer, $items, $description, $card);
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(402, $response->getHttpStatus(), 'HTTP Status 402 returned');
        
        $objects = $response->getObjects();
        $this->assertNotEmpty($objects, 'Parsed entities available');
        foreach ($objects as $object) {
            $this->assertInstanceOf('AceitaFacil\Entity\Error', $object, 'Parsed object is an Error');
        }
    }

    public function testGetPaymentInfo()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"id":"ab123456",
                              "organization_id":"1234",
                              "organization_name":"Acme",
                              "customer_id":"1",
                              "customer_name":"John Doe",
                              "description":"random purchase",
                              "customer_email":"johndoe@acme.com",
                              "chargetype":"AVISTA",
                              "paymentmethod":"CREDITCARD",
                              "payer":"CUSTOMER",
                              "attempt_count":1,
                              "attempted":true,
                              "closed":false,
                              "paid":true,
                              "period_start":"2014-06-24 00:00:00",
                              "period_end":"2014-06-24 00:00:00",
                              "total_amount":500,
                              "next_charge_attempt":"2014-06-25 00:00:00",
                              "items":[
                                    {"id":"item_1",
                                     "vendor_id":"1234",
                                     "vendor_name":"Acme",
                                     "provider_id":"1234",
                                     "provider_name":"Acme",
                                     "amount":456,
                                     "description":"Item 1",
                                     "trigger_lock":false },
                                    {"id":"item_2",
                                    "vendor_id":"aaaa",
                                    "vendor_name":"aceitaFacil",
                                    "provider_id":"aaaa",
                                    "provider_name":"aceitaFacil",
                                    "amount":44,
                                    "description":"Tarifa aceitaFacil",
                                    "trigger_lock":false}
                                    ]
                              }'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $response = $client->getPayment('ab123456');
        $this->assertFalse($response->isError(), 'Not an error');
        
        $payments = $response->getObjects();
        $this->assertNotEmpty($payments, 'Objects were filled');
        
        $payment = $payments[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'Payment is ok');
        $this->assertNotEmpty($payment->id, 'Transaction ID found');
        $this->assertNotEmpty($payment->description, 'Description found');
        $this->assertNotEmpty($payment->charge_type, 'Charge type found');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertGreaterThanOrEqual(0, $payment->attempt_count, 'Attempts count ok');
        $this->assertTrue($payment->paid, 'Actual payment was made');
        $this->assertInstanceOf('DateTime', $payment->period_start, 'Start period parsed');
        $this->assertInstanceOf('DateTime', $payment->period_end, 'End period parsed');
        $this->assertInstanceOf('DateTime', $payment->next_charge_attempt, 'Next charge attempt parsed');
        
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $this->assertNotEmpty($payment->items, 'Items were found');
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $payment->items[0], 'Items array consist of Item objects');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPaymentInfoNoId()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $response = $client->getPayment('');
    }
    
    public function testMakeBillPayment()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"id":"ab123456",
                              "organization_id":"1234",
                              "organization_name":"Acme",
                              "customer_id":"1",
                              "customer_name":"John Doe",
                              "description":"random purchase",
                              "customer_email":"johndoe@acme.com",
                              "chargetype":"AVISTA",
                              "paymentmethod":"BOLETO",
                              "payer":"CUSTOMER",
                              "attempt_count":0,
                              "attempted":false,
                              "closed":false,
                              "paid":false,
                              "period_start":"2014-06-24 00:00:00",
                              "period_end":"2014-06-24 00:00:00",
                              "total_amount":900,
                              "next_charge_attempt":null,
                              "items":[
                                    {"id":"item_1",
                                     "vendor_id":"1234",
                                     "vendor_name":"Acme",
                                     "provider_id":"1234",
                                     "provider_name":"Acme",
                                     "amount":856,
                                     "description":"Item 1",
                                     "trigger_lock":false },
                                    {"id":"item_2",
                                    "vendor_id":"aaaa",
                                    "vendor_name":"aceitaFacil",
                                    "provider_id":"aaaa",
                                    "provider_name":"aceitaFacil",
                                    "amount":44,
                                    "description":"Tarifa aceitaFacil",
                                    "trigger_lock":false}
                                    ],
                               "boleto":{
                                    "hash":"1234",
                                    "due_date":"2014-06-24",
                                    "url":"http:\/\/sandbox.boleto.aceitafacil.com\/1"
                               }
                              }'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
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
        
        $response = $client->makePayment($customer, $items, $description);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $payments = $response->getObjects();
        $this->assertNotEmpty($payments, 'Objects were filled');
        
        $payment = $payments[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'Payment is ok');
        $this->assertNotEmpty($payment->id, 'Transaction ID found');
        $this->assertNotEmpty($payment->description, 'Description found');
        $this->assertNotEmpty($payment->charge_type, 'Charge type found');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertGreaterThanOrEqual(0, $payment->attempt_count, 'Attempts count ok');
        $this->assertFalse($payment->paid, 'Actual payment was made');
        $this->assertInstanceOf('DateTime', $payment->period_start, 'Start period parsed');
        $this->assertInstanceOf('DateTime', $payment->period_end, 'End period parsed');
        $this->assertEmpty($payment->next_charge_attempt, 'There is no next charge attempt');
        
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $this->assertNotEmpty($payment->items, 'Items were found');
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $payment->items[0], 'Items array consist of Item objects');
        
        $this->assertEquals(array_reduce($items, function ($sum, $item) {return $sum+$item->amount;}), $payment->total_amount, 'Total amount found matches items');
        
        $this->assertNotEmpty($payment->bill, 'Payment Bill was found');
        $this->assertInstanceOf('AceitaFacil\Entity\Bill', $payment->bill, 'Bill is ok');
        $this->assertNotEmpty($payment->bill->hash, 'Bill hash parsed');
        $this->assertNotEmpty($payment->bill->url, 'Bill URL parsed');
        $this->assertInstanceOf('DateTime', $payment->bill->due_date, 'Bill due date parsed');
    }

    public function testMakeBillPaymentWithPushEndpoint()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"id":"ab123456",
                              "organization_id":"1234",
                              "organization_name":"Acme",
                              "customer_id":"1",
                              "customer_name":"John Doe",
                              "description":"random purchase",
                              "customer_email":"johndoe@acme.com",
                              "chargetype":"AVISTA",
                              "paymentmethod":"BOLETO",
                              "payer":"CUSTOMER",
                              "attempt_count":0,
                              "attempted":false,
                              "closed":false,
                              "paid":false,
                              "period_start":"2014-06-24 00:00:00",
                              "period_end":"2014-06-24 00:00:00",
                              "total_amount":900,
                              "next_charge_attempt":null,
                              "callback_url":"https://acme.com/endpoint",
                              "callback_code":"abcde",
                              "items":[
                                    {"id":"item_1",
                                     "vendor_id":"1234",
                                     "vendor_name":"Acme",
                                     "provider_id":"1234",
                                     "provider_name":"Acme",
                                     "amount":856,
                                     "description":"Item 1",
                                     "trigger_lock":false },
                                    {"id":"item_2",
                                    "vendor_id":"aaaa",
                                    "vendor_name":"aceitaFacil",
                                    "provider_id":"aaaa",
                                    "provider_name":"aceitaFacil",
                                    "amount":44,
                                    "description":"Tarifa aceitaFacil",
                                    "trigger_lock":false}
                                    ],
                               "boleto":{
                                    "hash":"1234",
                                    "due_date":"2014-06-24",
                                    "url":"http:\/\/sandbox.boleto.aceitafacil.com\/1"
                               }
                              }'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        $push_endpoint = 'https://acme.com/endpoint';
        $client->setPushEndpoint($push_endpoint);
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $vendor = new Entity\Vendor();
        $vendor->id = '1234';
        $vendor->name = 'Acme';
        
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
        $push_code = 'abcde';
        
        $response = $client->makePayment($customer, $items, $description, null, $push_code);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $payments = $response->getObjects();
        $this->assertNotEmpty($payments, 'Objects were filled');
        
        $payment = $payments[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'Payment is ok');
        $this->assertNotEmpty($payment->id, 'Transaction ID found');
        $this->assertNotEmpty($payment->description, 'Description found');
        $this->assertNotEmpty($payment->charge_type, 'Charge type found');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertGreaterThanOrEqual(0, $payment->attempt_count, 'Attempts count ok');
        $this->assertFalse($payment->paid, 'Actual payment was made');
        $this->assertInstanceOf('DateTime', $payment->period_start, 'Start period parsed');
        $this->assertInstanceOf('DateTime', $payment->period_end, 'End period parsed');
        $this->assertEmpty($payment->next_charge_attempt, 'There is no next charge attempt');
        $this->assertNotEmpty($payment->callback_url, 'Push endpoint was set');
        $this->assertEquals($push_endpoint, $payment->callback_url, 'Push endpoint was recognized');
        $this->assertNotEmpty($payment->callback_code, 'Push code was set');
        $this->assertEquals($push_code, $payment->callback_code, 'Push code was recognized');
        
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $this->assertNotEmpty($payment->items, 'Items were found');
        $this->assertInstanceOf('AceitaFacil\Entity\Item', $payment->items[0], 'Items array consist of Item objects');
        
        $this->assertEquals(array_reduce($items, function ($sum, $item) {return $sum+$item->amount;}), $payment->total_amount, 'Total amount found matches items');
        
        $this->assertNotEmpty($payment->bill, 'Payment Bill was found');
        $this->assertInstanceOf('AceitaFacil\Entity\Bill', $payment->bill, 'Bill is ok');
        $this->assertNotEmpty($payment->bill->hash, 'Bill hash parsed');
        $this->assertNotEmpty($payment->bill->url, 'Bill URL parsed');
        $this->assertInstanceOf('DateTime', $payment->bill->due_date, 'Bill due date parsed');
    }
}