<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSubscription()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $subscription = new Entity\Subscription();
        $subscription->id = time();
        $subscription->name = 'Acme Plan';
        $subscription->description = 'Acme Plan is awesome';
        $subscription->trial_days = 10;
        $subscription->interval_months = 1;
        $subscription->amount = 15;
        
        $response = $client->createSubscriptionPlan($subscription);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscriptions = $response->getObjects();
        $this->assertNotEmpty($subscriptions, 'Objects were filled');
        
        $subscription = $subscriptions[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
        $this->assertEquals(1, $subscription->interval_months, 'Subscription interval match');
        $this->assertEquals(10, $subscription->trial_days, 'Subscription trial match');
        
        return $subscription;
    }

    /**
     * @depends testCreateSubscription
     */
    public function testGetSubscription($original_subscription)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $response = $client->getSubscriptionPlan($original_subscription->id);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscriptions = $response->getObjects();
        $this->assertNotEmpty($subscriptions, 'Objects were filled');
        
        $subscription = $subscriptions[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertEquals($original_subscription->id, $subscription->id, 'Original and found subscriptions ID match');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
        $this->assertEquals(floatval(15), $subscription->amount, 'Amount matches a float 15.00');
        $this->assertEquals(1, $subscription->interval_months, 'Subscription interval match');
        $this->assertEquals(10, $subscription->trial_days, 'Subscription trial match');
        
        return $subscription;
    }

    /**
     * @depends testGetSubscription
     */
    public function testCreateSameSubscriptionPlan($original_subscription)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $subscription = new Entity\Subscription();
        $subscription->id = $original_subscription->id;
        $subscription->name = 'Acme Plan';
        $subscription->description = 'Acme Plan is awesome';
        $subscription->trial_days = 10;
        $subscription->interval_months = 1;
        $subscription->amount = 15;
        
        $response = $client->createSubscriptionPlan($subscription);
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(409, $response->getHttpStatus(), 'HTTP status 409 returned');
    }
    
    /**
     * @depends testGetSubscription
     */
    public function testUpdateSubscription($original_subscription)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $subscription = new Entity\Subscription();
        $subscription->id = $original_subscription->id;
        $subscription->name = 'Acme Plan 2';
        $subscription->description = 'Acme Plan is awesome';
        $subscription->trial_days = 10;
        $subscription->interval_months = 2;
        $subscription->amount = 15;
        
        $response = $client->updateSubscriptionPlan($subscription);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscriptions = $response->getObjects();
        $this->assertNotEmpty($subscriptions, 'Objects were filled');
        
        $subscription = $subscriptions[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertEquals($original_subscription->id, $subscription->id, 'Original and found subscriptions ID match');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertEquals('Acme Plan 2', $subscription->name, 'Name changed to Acme Plan 2');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
        $this->assertEquals(floatval(15), $subscription->amount, 'Amount matches a float 15.00');
        $this->assertEquals(2, $subscription->interval_months, 'Subscription interval changed to 2');
        $this->assertEquals(10, $subscription->trial_days, 'Subscription trial match');
    }
}