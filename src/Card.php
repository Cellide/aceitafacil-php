<?php

namespace AceitaFacil;

/**
 * Card information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Card
{
    /**
     * Card's API token
     * 
     * @var string
     */
    private $token;
    
    /**
     * Card's brand
     * 
     * @var string
     */
    private $brand;
    
    /**
     * Card's last digits
     * 
     * @var string
     */
    private $last_digits;
    
    /**
     * Creates a card from a JSON response
     * 
     * @param  mixed     $json          Decoded json object with card details
     * @return self
     */
    public function __construct($json)
    {
        $this->token = $json['token'];
        $this->brand = (isset($json['card_brand'])) ? $json['card_brand'] : '';
        $this->last_digits = (isset($json['last_digits'])) ? $json['last_digits'] : '';
    }
    
    /**
     * Get a token referencing this saved card
     * 
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Get this card's brand
     * 
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }
    
    /**
     * Get this card's last 4 digits
     * 
     * @return string
     */
    public function getLastDigits()
    {
        return $this->last_digits;
    }
}