<?php

namespace AceitaFacil;

/**
 * Response wrapper for AceitaFácil's API concerning Cards information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class ResponseCard extends Response
{
    /**
     * Card's API token
     * 
     * @var string
     */
    private $token;
    
    /**
     * Card's issuer
     * 
     * @var string
     */
    private $issuer;
    
    /**
     * Card's last digits
     * 
     * @var string
     */
    private $last_digits;
    
    /**
     * Wraps an API response
     * 
     * Must be called by {parse()}
     * 
     * @param  int       $http_status   HTTP status code
     * @param  mixed[]   $json          Decoded json object with response details
     * @return self
     * @throws InvalidArgumentException
     */
    protected function __construct($http_status, $json)
    {
        parent::__construct($http_status, $json);
        
        if (!isset($this->json['card']))
            throw new InvalidArgumentException('Response is not a valid Card object');
        
        $card = $this->json['card'];
        $this->token = $card['token'];
        $this->issuer = $card['issuer'];
        $this->last_digits = $card['last_digits'];
    }
    
    public function getToken()
    {
        return $this->token;
    }
    
    public function getIssuer()
    {
        return $this->issuer;
    }
    
    public function getLastDigits()
    {
        return $this->last_digits;
    }
}