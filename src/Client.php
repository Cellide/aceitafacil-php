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
     * @var GuzzleHttp\Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $username;
    
    /**
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
     * @throws RuntimeException if not initialized
     */
    private function request($method, $endpoint, $data)
    {
        if (empty($this->username) || empty($this->password))
            throw new \RuntimeException('Client not properly initialized');
        
        $full_data = array(
            'auth' => array($this->username, $this->password),
            'body' => $data
        );
        $request = $this->client->createRequest($method, $endpoint, $full_data);
        
        $response = null;
        try {
            $response = $this->client->send($request);
        }
        catch (\GuzzleHttp\Exception\TransferException $e) {
            $response = $e->getResponse();
        }
        return Response::parse($response);
    }
    
    /**
     * Saves a card info
     * 
     * @param  string $card       Cardholder's name.
     * @param  string $number     Card's number
     * @param  string $cvv        Card's CVV
     * @param  string $exp_date   Card's expiration date, formatted as YYYYMM
     * @return ResponseCard
     */
    public function saveCard($name, $number, $cvv, $exp_date)
    {
        if (empty($name) || empty($number) || empty($cvv) || empty($exp_date))
            throw new \InvalidArgumentException('Card info missing');
        
        $data = array(
            'customer[id]' => $this->username,
            'card[number]' => $number,
            'card[name]' => $name,
            'card[cvv]' => $cvv,
            'card[exp_date]' => $exp_date
        );
        return $this->request('POST', '/card', $data);
    }
}
