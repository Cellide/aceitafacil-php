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
     * JSON object from the response
     * 
     * @var object
     */
    protected $json;
    
    /**
     * Returns the concrete response which should be instantiated
     * 
     * @param  GuzzleHttp\Message\ResponseInterface   $response
     * @return self
     * @throws RuntimeException
     */
    public static function parse(GuzzleHttp\Message\ResponseInterface $response)
    {
        $http_status = intval($response->getStatusCode());
        $is_error = !($http_status >= 200 && $http_status < 300);
        $json = $response->json();
        
        if ($is_error)
            return new ResponseError($http_status, $json);
        else if (isset($json['card']))
            return new ResponseCard($http_status, $json);
        else
            throw new \RuntimeException('Could not parse response type');
    }
    
    /**
     * Wraps an API response
     * 
     * Must be called by {parse()}
     * 
     * @param  int       $http_status   HTTP status code
     * @param  mixed[]   $json          Decoded json object with response details
     * @return self
     */
    protected function __construct($http_status, $json)
    {
        $this->http_status = $http_status;
        $this->is_error = !($this->http_status >= 200 && $this->http_status < 300);
        $this->json = $json;
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
}