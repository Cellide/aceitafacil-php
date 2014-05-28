<?php

namespace Cellide\AceitaFacil;

/**
 * Error response wrapper for AceitaFácil's API
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class ResponseError extends Response
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
            throw new \InvalidArgumentException('Response is not a valid Error object');
        
        foreach ($json['errors'] as $error) {
            $this->errors[] = $error;
        }
    }
}