<?php

namespace AceitaFacil;

/**
 * Request wrapper for AceitaFácil's API
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Client
{
    /**
     * @var string
     */
    const SANDBOX_URL = 'https://sandbox.api.aceitafacil.com';
    
    /**
     * @var string
     */
    const PRODUCTION_URL = 'https://api.aceitafacil.com';
    
    /**
     * Guzzle client
     * 
     * @var GuzzleHttp\Client
     */
    private $client;
    
    /**
     * Username/App ID
     * 
     * @var string
     */
    private $username;
    
    /**
     * Password/App secret
     * 
     * @var string
     */
    private $password;
    
    /**
     * Client contructor
     * 
     * @param  bool   $is_sandbox     If true, sandobox environment is used. Default: false.
     * @param  mixed  $mock_adapter    Optionally use this \GuzzleHttp\Adapter\MockAdapter for requests
     * @return self
     */
    public function __construct($is_sandbox = false, $mock_adapter = null)
    {
        $options = (!empty($mock_adapter)) ?
                        array('adapter' => $mock_adapter) :
                        array('base_url' => ($is_sandbox ? self::SANDBOX_URL : self::PRODUCTION_URL));
        $this->client = new \GuzzleHttp\Client($options);
    }
    
    /**
     * Initializes the Client with a customer's username and password (public and private keys)
     * 
     * Must be called before any other method
     * 
     * @param  string    $username    Username (public key)
     * @param  string    $password    Password (private key)
     * @return void
     * @throws \InvalidArgumentException if not called correctly
     */
    public function init($username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('Username and password must be set');
        }
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Wrapper for API calls
     * 
     * @param  string  $method    HTTP method (POST, GET)
     * @param  string  $endpoint  API endpoint
     * @param  mixed[] $data      Data to be sent
     * @return Response
     * @throws \RuntimeException if not initialized
     */
    private function request($method, $endpoint, $data = null)
    {
        if (empty($this->username) || empty($this->password))
            throw new \RuntimeException('Client not properly initialized');
        
        $full_data = array(
            'auth' => array($this->username, $this->password),
            'body' => (!empty($data) ? $data : array())
        );
        $request = $this->client->createRequest($method, $endpoint, $full_data);
        
        $response = null;
        try {
            $response = $this->client->send($request);
        }
        catch (\GuzzleHttp\Exception\TransferException $e) {
            $response = $e->getResponse();
        }
        return new Response($response);
    }
    
    /**
     * Saves a card info
     * 
     * Card must contain holder's name, number, and expiration date
     * 
     * @param  \AceitaFacil\Entity\Card    $card
     * @return \AceitaFacil\Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function saveCard(Entity\Card $card)
    {
        if (empty($card->name) || empty($card->number) || empty($card->exp_date))
            throw new \InvalidArgumentException('Card info missing');
        
        $data = array(
            'customer[id]' => $this->username,
            'card[number]' => $card->number,
            'card[name]' => $card->name,
            'card[exp_date]' => $card->exp_date
        );
        return $this->request('POST', '/card', $data);
    }
    
    /**
     * Get all cards stored
     * 
     * @return Response
     */
    public function getAllCards()
    {
        return $this->request('GET', '/card');
    }
    
    /**
     * Delete a stored card
     * 
     * @param  string $token      Card's reference token
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function deleteCard($token)
    {
        if (empty($token))
            throw new \InvalidArgumentException('Card token missing');
        
        $data = array(
            'customer[id]' => $this->username,
            'card[token]' => $token
        );
        return $this->request('DELETE', '/card', $data);
    }
    
    /**
     * Make a payment using a card
     * 
     * Card must contain token and CVV
     * 
     * @param  Entity\Card       $card
     * @param  Entity\Customer   $customer
     * @param  string            $description
     * @param  float             $total_amount
     * @param  Entity\Item       $items
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function makePayment(Entity\Card $card, Entity\Customer $customer, $description, $total_amount, $items)
    {
        if (empty($card) || empty($card->token) || empty($card->cvv))
            throw new \InvalidArgumentException('Card info missing');
        if (empty($customer) || empty($customer->id) || empty($customer->email) || empty($customer->name) || empty($customer->language))
            throw new \InvalidArgumentException('Customer info missing');
        if (empty($description))
            throw new \InvalidArgumentException('Description missing');
        if (empty($total_amount) || !is_numeric($total_amount) || floatval($total_amount) <= 0)
            throw new \InvalidArgumentException('Invalid amount');
        if (empty($items))
            throw new \InvalidArgumentException('Items missing');
        
        $total_amount = intval(floatval($total_amount)*100);
        $data = array(
            'customer[id]' => $this->username,
            'description' => $description,
            'paymentmethod[id]' => 1,
            'total_amount' => $total_amount,
            
            'card[token]' => $card->token,
            'card[cvv]' => $card->cvv,
            
            'customer[id]' => $customer->id,
            'customer[email]' => $customer->email,
            'customer[name]' => $customer->name,
            'customer[email_language]' => $customer->language,
        );
        return $this->request('POST', '/payment', $data);
    }
}
