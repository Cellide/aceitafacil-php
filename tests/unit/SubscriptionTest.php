<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSubscriptionInfoMissing()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $response = $client->getSubscriptionPlan('');
    }
    
    public function testGetSubscription()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"subscription_plan":{
                                "id":"123456",
                                "name":"Acme Plan",
                                "description":"Acme Plan is awesome",
                                "amount":1500,
                                "interval_days":0,
                                "interval_months":1,
                                "interval_years":0,
                                "trial_days":10
                             }}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $response = $client->getSubscriptionPlan('123456');
        $this->assertFalse($response->isError(), 'Not an error');
        
        $subscriptions = $response->getObjects();
        $this->assertNotEmpty($subscriptions, 'Objects were filled');
        
        $subscription = $subscriptions[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Subscription', $subscription, 'Object is a subscription');
        $this->assertNotEmpty($subscription->id, 'Id found');
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
    }

    public function testCreateSubscription()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"subscription_plan":{
                                "id":"123456",
                                "name":"Acme Plan",
                                "description":"Acme Plan is awesome",
                                "amount":1500,
                                "interval_days":0,
                                "interval_months":1,
                                "interval_years":0,
                                "trial_days":10
                             }}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $subscription = new Entity\Subscription();
        $subscription->id = 1;
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
    }

    public function testUpdateSubscription()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"subscription_plan":{
                                "id":"123456",
                                "name":"Acme Plan",
                                "description":"Acme Plan is awesome",
                                "amount":1500,
                                "interval_days":0,
                                "interval_months":2,
                                "interval_years":0,
                                "trial_days":10
                             }}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $subscription = new Entity\Subscription();
        $subscription->id = 1;
        $subscription->name = 'Acme Plan';
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
        $this->assertNotEmpty($subscription->name, 'Name found');
        $this->assertNotEmpty($subscription->description, 'Description found');
        $this->assertNotEmpty($subscription->amount, 'Amount found');
    }
}