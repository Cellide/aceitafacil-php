<?php

namespace AceitaFacil;

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
     */
    protected function __construct($http_status, $json)
    {
        parent::__construct($http_status, $json);
        
        if (empty($this->json) || !isset($this->json['errors'])) {
            $this->errors[] = array('message' => "Error $http_status", 'name' => 'INVALID REQUEST', 'at' => '');
        } else {
            foreach ($this->json['errors'] as $error) {
                $this->errors[] = $error;
            }
        }
    }
    
    /**
     * Return all errors
     * 
     * @return mixed[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}