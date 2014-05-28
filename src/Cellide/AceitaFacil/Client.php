<?php

namespace Cellide\AceitaFacil;

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
     * @return self
     */
    public function __construct($is_sandbox = false)
    {
        $this->client = new \GuzzleHttp\Client(array('base_url' => ($is_sandbox ? self::SANDBOX_URL : self::PRODUCTION_URL)));
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
        if (empty($this->username) || empty($this->password))
            throw new \RuntimeException('Client not properly initialized');
        
        $request = $client->createRequest('POST', '/card', array(
            'auth' => array($this->username, $this->password),
            'json' => array(
                'customer' => array(
                    'id' => $this->password
                ),
                'card' => array(
                    'number' => $number,
                    'name' => $name,
                    'cvv' => $cvv,
                    'exp_date' => $exp_date
                )
            )
        ));
        $reponse = $this->client->send($request);
        return Response::parse($response);
    }
}
