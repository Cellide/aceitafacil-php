<?php

namespace AceitaFacil;

/**
 * Response wrapper for AceitaFácil's API
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Response
{
    /**
     * If the response was an error
     * 
     * @var bool
     */
    private $is_error;
    
    /**
     * HTTP status returned
     * 
     * @var int
     */
    private $http_status;
    
    /**
     * List of error objects
     * 
     * @var mixed[]
     */
    private $errors;
    
    /**
     * Decoded success response body
     * 
     * @var mixed[]
     */
    private $body;
    
    /**
     * Parsed response with one or more entities
     * 
     * @var mixed[]
     */
    private $objects = array();
    
    /**
     * Wraps an API response
     * 
     * @param  GuzzleHttp\Message\ResponseInterface   $response
     * @return self
     */
    public function __construct(\GuzzleHttp\Message\ResponseInterface $response)
    {
        $this->http_status = intval($response->getStatusCode());
        $this->is_error = !($this->http_status >= 200 && $this->http_status < 300);
        try {
            $this->body = $response->json();
        }
        catch (\GuzzleHttp\Exception\ParseException $e) { }
        
        if ($this->isError()) {
            if (empty($this->body) || !isset($this->body['errors'])) {
                $this->errors[] = array('message' => "Error $this->http_status", 'name' => 'INVALID REQUEST', 'at' => '');
            } else {
                foreach ($this->body['errors'] as $error) {
                    $this->errors[] = $error;
                }
            }
        } else {
            $this->parse();
        }
    }
    
    /**
     * Parses the response body into known entities
     * 
     * @throws RuntimeException if response wasn't recognized
     */
    private function parse()
    {
        if (isset($this->body['card'])) {
            $cards = $this->body['card'];
            foreach ($cards as $card) {
                $this->objects[] = new Card($card);
            }
        } else {
            throw new \RuntimeException('Could not recognize response type');
        }
    }
    
    /**
     * If the response was unsuccessful
     * 
     * @return bool
     */
    public function isError()
    {
        return $this->is_error;
    }
    
    /**
     * Returns the HTTP status code
     * 
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->http_status;
    }
    
    /**
     * Returns response objects when successful
     * 
     * @return mixed[]
     */
    public function getObjects()
    {
        return $this->objects;
    }
    
    /**
     * Returns all errors
     * 
     * @return mixed[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}