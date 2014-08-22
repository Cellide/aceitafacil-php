<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class SubscribeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetSubscribeInfoMissing()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $response = $client->getSubscribe('');
    }
    
    public function testSubscribe()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"description":"John Doe subscription to Acme Plan",
                              "customer":{
                                  "id":"1",
                                  "name":"John Doe"
                              },
                              "organization":{
                                  "id":"1",
                                  "name":"Acme"
                              },
                              "paymentmethod":{
                                  "id":1
                              },
                              "subscription_plan":{
                                  "id":"1",
                                  "name":"Acme Plan",
                                  "description":"Acme Plan is awesome",
                                  "amount":1000,
                                  "interval_days":15,
                                  "interval_months":0,
                                  "interval_years":0,
                                  "trial_days":15
                              },
                              "callback_url":"",
                              "callback_code":"",
                              "signin_date":"2014-08-01",
                              "next_invoice_date":"2014-08-16"
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
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $response = $client->subscribe($customer, 1, "John Doe subscription to Acme Plan", $card);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscribe_info = $response->getObjects();
        $this->assertNotEmpty($subscribe_info, 'Objects were filled');
        $this->assertEquals(2, count($subscribe_info), 'Subscribe method must return two response objects');
        
        $payment = $subscribe_info[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'First object is a payment');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertInstanceOf('DateTime', $payment->signin_date, 'Sign-in date parsed');
        $this->assertInstanceOf('DateTime', $payment->next_invoice_date, 'Next invoice date parsed');
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $subscription = $subscribe_info[1];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Second object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
    }

    public function testUpdateSubscribe()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"description":"John Doe subscription to Acme Plan changed",
                              "customer":{
                                  "id":"1",
                                  "name":"John Doe"
                              },
                              "organization":{
                                  "id":"1",
                                  "name":"Acme"
                              },
                              "paymentmethod":{
                                  "id":2
                              },
                              "subscription_plan":{
                                  "id":"1",
                                  "name":"Acme Plan",
                                  "description":"Acme Plan is awesome",
                                  "amount":1000,
                                  "interval_days":15,
                                  "interval_months":0,
                                  "interval_years":0,
                                  "trial_days":15
                              },
                              "callback_url":"",
                              "callback_code":"",
                              "signin_date":"2014-08-01",
                              "next_invoice_date":"2014-08-16"
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
        
        $response = $client->updateSubscribe($customer, 1, "John Doe subscription to Acme Plan changed");
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscribe_info = $response->getObjects();
        $this->assertNotEmpty($subscribe_info, 'Objects were filled');
        $this->assertEquals(2, count($subscribe_info), 'Subscribe method must return two response objects');
        
        $payment = $subscribe_info[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'First object is a payment');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertInstanceOf('DateTime', $payment->signin_date, 'Sign-in date parsed');
        $this->assertInstanceOf('DateTime', $payment->next_invoice_date, 'Next invoice date parsed');
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $subscription = $subscribe_info[1];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Second object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
    }

    public function testGetSubscribe()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"description":"John Doe subscription to Acme Plan",
                              "customer":{
                                  "id":"1",
                                  "name":"John Doe"
                              },
                              "organization":{
                                  "id":"1",
                                  "name":"Acme"
                              },
                              "paymentmethod":{
                                  "id":1
                              },
                              "subscription_plan":{
                                  "id":"1",
                                  "name":"Acme Plan",
                                  "description":"Acme Plan is awesome",
                                  "amount":1000,
                                  "interval_days":15,
                                  "interval_months":0,
                                  "interval_years":0,
                                  "trial_days":15
                              },
                              "callback_url":"",
                              "callback_code":"",
                              "signin_date":"2014-08-01",
                              "next_invoice_date":"2014-08-16"
                            }'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $response = $client->getSubscribe(1);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscribe_info = $response->getObjects();
        $this->assertNotEmpty($subscribe_info, 'Objects were filled');
        $this->assertEquals(2, count($subscribe_info), 'Subscribe method must return two response objects');
        
        $payment = $subscribe_info[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'First object is a payment');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertInstanceOf('DateTime', $payment->signin_date, 'Sign-in date parsed');
        $this->assertInstanceOf('DateTime', $payment->next_invoice_date, 'Next invoice date parsed');
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        
        $subscription = $subscribe_info[1];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Second object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
    }

    public function testCancelSubscribe()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"deleted":true}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $response = $client->cancelSubscribe(1);
        $this->assertFalse($response->isError(), 'Not an error');
        $this->assertEmpty($response->getObjects(), 'No response objects');
    }

    public function testSubscribeWithPushEndpoint()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"description":"John Doe subscription to Acme Plan",
                              "customer":{
                                  "id":"1",
                                  "name":"John Doe"
                              },
                              "organization":{
                                  "id":"1",
                                  "name":"Acme"
                              },
                              "paymentmethod":{
                                  "id":1
                              },
                              "subscription_plan":{
                                  "id":"1",
                                  "name":"Acme Plan",
                                  "description":"Acme Plan is awesome",
                                  "amount":1000,
                                  "interval_days":15,
                                  "interval_months":0,
                                  "interval_years":0,
                                  "trial_days":15
                              },
                              "callback_url":"https://acme.com/endpoint",
                              "callback_code":"abcde",
                              "signin_date":"2014-08-01",
                              "next_invoice_date":"2014-08-16"
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
        
        $card = new Entity\Card();
        $card->token = '1234567';
        $card->cvv = '123';
        
        $push_code = 'abcde';
        
        $response = $client->subscribe($customer, 1, "John Doe subscription to Acme Plan", $card, $push_code);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscribe_info = $response->getObjects();
        $this->assertNotEmpty($subscribe_info, 'Objects were filled');
        $this->assertEquals(2, count($subscribe_info), 'Subscribe method must return two response objects');
        
        $payment = $subscribe_info[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Payment', $payment, 'First object is a payment');
        $this->assertNotEmpty($payment->payment_method, 'Payment method found');
        $this->assertInstanceOf('DateTime', $payment->signin_date, 'Sign-in date parsed');
        $this->assertInstanceOf('DateTime', $payment->next_invoice_date, 'Next invoice date parsed');
        $this->assertNotEmpty($payment->organization, 'Organization found');
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $payment->organization, 'Organization is a Vendor');
        $this->assertNotEmpty($payment->customer, 'Customer found');
        $this->assertInstanceOf('AceitaFacil\Entity\Customer', $payment->customer, 'Customer is ok');
        $this->assertNotEmpty($payment->callback_url, 'Push endpoint was set');
        $this->assertEquals($push_endpoint, $payment->callback_url, 'Push endpoint was recognized');
        $this->assertNotEmpty($payment->callback_code, 'Push code was set');
        $this->assertEquals($push_code, $payment->callback_code, 'Push code was recognized');
        
        $subscription = $subscribe_info[1];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Second object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
    }
}
        