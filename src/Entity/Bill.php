<?php

namespace AceitaFacil\Entity;

/**
 * Bank payment bill information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Bill
{
    /**
     * Hash code
     * 
     * @var string
     */
    public $hash;
    
    /**
     * Due date
     * 
     * @var DateTime
     */
    public $due_date;
    
    /**
     * URL
     * 
     * @var string
     */
    public $url;
    
    /**
     * Parses a JSON object into a Bill
     * 
     * @param  mixed[]  $json
     * @return Bill
     */
    public static function parse($json)
    {
        $entity = new Bill();
        $entity->hash = isset($json['hash']) ? $json['hash'] : null;
        $entity->url = isset($json['url']) ? $json['url'] : null;
        $entity->due_date = isset($json['due_date']) ? \DateTime::createFromFormat('Y-m-d', $json['due_date'], new \DateTimeZone('America/Sao_Paulo')) : null;
        return $entity;
    }
}