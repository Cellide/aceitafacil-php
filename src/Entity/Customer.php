<?php

namespace AceitaFacil\Entity;

/**
 * Customer information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Customer
{
    /**
     * ID
     * 
     * @var string
     */
    public $id;
    
    /**
     * Full name
     * 
     * @var string
     */
    public $name;
    
    /**
     * Email
     * 
     * @var string
     */
    public $email;
    
    /**
     * Language
     * 
     * @var string
     */
    public $language;
    
    /**
     * Parses a JSON object into a Customer
     * 
     * @param  mixed[]  $json
     * @return Customer
     */
    public static function parse($json)
    {
        $entity = new Customer();
        $entity->id = isset($json['customer_id']) ? $json['customer_id'] : null;
        $entity->name = isset($json['customer_name']) ? $json['customer_name'] : null;
        $entity->email = isset($json['customer_email']) ? $json['customer_email'] : null;
        $entity->language = isset($json['customer_language']) ? $json['customer_language'] : null;
        return $entity;
    }
}