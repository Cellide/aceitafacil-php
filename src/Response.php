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
     * @param  \GuzzleHttp\Message\ResponseInterface   $response
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
        
        $this->parse();
    }
    
    /**
     * Parses the response body into known entities
     * 
     * @throws RuntimeException if response wasn't recognized
     */
    private function parse()
    {
        if ($this->isError()) {
            // error parsing
            if (empty($this->body) || !isset($this->body['errors'])) {
                // response body isn't an error object, return a custom one
                $error = new Entity\Error();
                $error->message = "Error $this->http_status";
                $error->name = "INVALID REQUEST";
                $error->at = "";
                $this->objects[] = $error;
            } else {
                // parse error
                $errors = $this->body['errors'];
                foreach ($errors as $error) {
                    $this->objects[] = Entity\Error::parse($error);
                }
            }
        } else if (isset($this->body['card'])) {
            // card parsing
            $cards = $this->body['card'];
            foreach ($cards as $card) {
                $this->objects[] = Entity\Card::parse($card);
            }
        } else if (isset($this->body['paymentmethod'])) {
            // payment parsing
            $this->objects[] = Entity\Payment::parse($this->body);
        } else if (isset($this->body['vendor'])) {
            // vendor parsing
            $this->objects[] = Entity\Vendor::parse($this->body['vendor']);
        } else if (isset($this->body['item'])) {
            // item parsing
            $this->objects[] = Entity\Item::parse($this->body['item']);
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
}