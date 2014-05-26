<?php

namespace AceitaFacil;

/**
 * Response wrapper for AceitaFácil's API concerning Cards information
 * 
 * Should be used as an array (by accessing `$response['token']`) 
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class ResponseCard extends Response implements ArrayAccess
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
     * @param  GuzzleHttp\Message\ResponseInterface   $response
     * @return self
     */
    public function __construct(GuzzleHttp\Message\ResponseInterface $response)
    {
        parent::__construct($response);
        
        if (!isset($this->json['card']))
            throw new InvalidArgumentException('Response is not a valid Card object');
        
        $card = $this->json['card'];
        $this->token = $card['token'];
        $this->issuer = $card['issuer'];
        $this->last_digits = $card['last_digits'];
    }
    
    public function offsetSet($offset, $value) {
        throw new BadMethodCallException('Responses are read-only');
    }
    
    public function offsetUnset($offset) {
        throw new BadMethodCallException('Responses are read-only');
    }
    
    public function offsetExists($offset) {
        return ($offset == 'token' || $offset == 'issuer' || $offset == 'last_digits');
    }
    
    public function offsetGet($offset) {
        return isset($this->$offset) ? $this->$offset : null;
    }
}