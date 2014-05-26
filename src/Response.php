<?php

namespace AceitaFacil;

/**
 * Response wrapper for AceitaFácil's API
 * 
 * Must be extended to a class implementing a concrete response
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
abstract class Response
{
    /**
     * If the response was a successful one
     * 
     * @var bool
     */
    private $is_successful;
    
    /**
     * HTTP status returned
     * 
     * @var int
     */
    private $http_status;
    
    /**
     * JSON object from the response
     * 
     * @var object
     */
    protected $json;
    
    /**
     * Wraps an API response
     * 
     * @param  GuzzleHttp\Message\ResponseInterface   $response
     * @return self
     */
    public function __construct(GuzzleHttp\Message\ResponseInterface $response)
    {
        $this->http_status = intval($response->getStatusCode());
        $this->is_successful = ($this->http_status >= 200 && $this->http_status < 300);
        
        $this->json = $response->json();
    }
    
    /**
     * If the response was successful
     * 
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->is_successful;
    }
    
    /**
     * Return the HTTP response status
     * 
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->http_status;
    }
}