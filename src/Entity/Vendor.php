<?php

namespace AceitaFacil\Entity;

/**
 * Vendor information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Vendor
{
    /**
     * Vendor ID
     * 
     * @var string
     */
    public $id;
    
    /**
     * Vendor name
     * 
     * @var string
     */
    public $name;
    
    /**
     * Vendor email
     * 
     * @var string
     */
    public $email;
    
    /**
     * Vendor bank accounts
     * 
     * @var Bank[]
     */
    public $banks = array();
    
    /**
     * Parses a JSON object into a Vendor
     * 
     * @param  mixed[]  $json
     * @return Vendor
     */
    public static function parse($json)
    {
        $entity = new Vendor();
        $entity->id = isset($json['id']) ? $json['id'] : null;
        $entity->name = isset($json['name']) ? $json['name'] : null;
        $entity->email = isset($json['email']) ? $json['email'] : null;
        
        if (isset($json['bank']) && !empty($json['bank'])) {
            foreach ($json['bank'] as $bank) {
                $entity->banks[] = Bank::parse($bank);
            }
        }
        
        return $entity;
    }
}