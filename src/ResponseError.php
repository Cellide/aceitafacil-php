<?php

namespace AceitaFacil;

/**
 * Error response wrapper for AceitaFácil's API
 * 
 * Should be used as an multi-dimension array:
 * each index will be an array of error info
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class ResponseError extends Response implements ArrayAccess
{
    /**
     * List of error objects
     * 
     * @var mixed[]
     */
    private $errors;
    
    /**
     * Wraps an API error response
     * 
     * @param  int       $http_status   HTTP status code
     * @param  mixed[]   $json          Decoded json object with response details
     * @return self
     * @throws InvalidArgumentException
     */
    protected function __construct($http_status, $json)
    {
        if (!isset($json['errors']))
            throw new InvalidArgumentException('Response is not a valid Error object');
        
        foreach ($json['errors'] as $error) {
            $this->errors[] = $error;
        }
    }
    
    public function offsetExists($offset) {
        return parent::offsetExists($offset) || isset($this->errors[$offset]);
    }
    
    public function offsetGet($offset) {
        if ($offset == 'http_status')
            return $this->getHttpStatus();
        else
            return isset($this->errors[$offset]) ? $this->errors[$offset] : null;
    }
}