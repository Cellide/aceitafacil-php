<?php

namespace AceitaFacil\Entity;

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
     * Card API token
     * 
     * @var string
     */
    public $token;
    
    /**
     * Card brand
     * 
     * @var string
     */
    public $brand;
    
    /**
     * Card number
     * 
     * @var string
     */
    public $number;
    
    /**
     * Cardholder's name as printed on card
     * 
     * @var string
     */
    public $name;
    
    /**
     * Card last digits
     * 
     * @var string
     */
    public $last_digits;
    
    /**
     * Card CVV
     * 
     * @var string
     */
    public $cvv;
    
    /**
     * Card expiration date
     * 
     * Formatted as YYYYMM
     * 
     * @var string
     */
    public $exp_date;
    
    /**
     * Parses a JSON object into a Card
     * 
     * @param  mixed[]  $json
     * @return Card
     */
    public static function parse($json)
    {
        $card = new Card();
        $card->token = $json['token'];
        $card->last_digits = isset($json['last_digits']) ? $json['last_digits'] : null;
        $card->brand = isset($json['card_brand']) ? $json['card_brand'] : null;
        return $card;
    }
}