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
     * Create a Subscription plan to be subscribed in these tests
     */
    public function testCreateSubscription()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $subscription = new Entity\Subscription();
        $subscription->id = 1;
        $subscription->name = 'Acme Plan';
        $subscription->description = 'Acme Plan is awesome';
        $subscription->trial_days = 10;
        $subscription->interval_months = 1;
        $subscription->amount = 15;
        
        $response = $client->createSubscriptionPlan($subscription);
        
        // Most likely this plan will already exists, which is fine.
        // So we will only test for client request errors
        $this->assertNotEquals(400, $response->getHttpStatus(), 'HTTP Status must not be 400');
        
        return $subscription;
    }
    
    /**
     * @depends testCreateSubscription
     */
    public function testSubscribe($original_subscription)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        $push_endpoint = 'https://acme.com/endpoint';
        $client->setPushEndpoint($push_endpoint);
        
        $customer = new Entity\Customer();
        $customer->id = 1;
        $customer->name = 'John Doe';
        $customer->email = 'johndoe@mailinator.com';
        $customer->language = 'EN';
        
        $push_code = 'abcde';
        
        $response = $client->subscribe($customer, $original_subscription->id, null, null, $push_code);
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
        
        return $customer;
    }

    /**
     * @depends testSubscribe
     */
    public function testUpdateSubscribe($customer)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
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
        
        return $customer;
    }

    /**
     * @depends testUpdateSubscribe
     */
    public function testGetSubscribe($customer)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $response = $client->getSubscribe($customer->id);
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
        
        return $customer;
    }

    /**
     * @depends testGetSubscribe
     */
    public function testCancelSubscribe($customer)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $response = $client->cancelSubscribe($customer->id);
        $this->assertFalse($response->isError(), 'Not an error');
        $this->assertEmpty($response->getObjects(), 'No response objects');
    }

    /**
     * Tested subscribe must be teared down because a failed test run will likely leave
     * a subscribe in place
     */
    public static function tearDownAfterClass()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $response = $client->cancelSubscribe(1);
    }
}
        