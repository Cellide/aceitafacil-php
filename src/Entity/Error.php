<?php

namespace AceitaFacil\Entity;

/**
 * Error information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Error
{
    /**
     * Error message
     * 
     * @var string
     */
    public $message;
    
    /**
     * Error name/code
     * 
     * @var string
     */
    public $name;
    
    /**
     * Error line number
     * 
     * @var string
     */
    public $at;
    
    /**
     * Parses a JSON object into an Error
     * 
     * @param  mixed[]  $json
     * @return Error
     */
    public static function parse($json)
    {
        $entity = new Error();
        $entity->message = isset($json['message']) ? $json['message'] : null;
        $entity->name = isset($json['name']) ? $json['name'] : null;
        $entity->at = isset($json['at']) ? $json['at'] : null;
        return $entity;
    }
}