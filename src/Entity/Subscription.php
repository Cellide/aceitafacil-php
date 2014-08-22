<?php

namespace AceitaFacil\Entity;


/**
 * Subscription plan
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Subscription
{
    /**
     * Subscription plan ID
     * 
     * @var string
     */
    public $id;
    
    /**
     * Subscription name
     * 
     * @var string
     */
    public $name;
    
    /**
     * Subscription description
     * 
     * Will be used when describing in a Bill (\AceitaFacil\Entity\Bill)
     * 
     * @var string
     */
    public $description;
    
    /**
     * Subscription amount
     * 
     * @var float
     */
    public $amount;
    
    /**
     * Subscription interval, in days
     * 
     * Defaults to 0 if interval is not defined in days
     * 
     * @var int
     */
    public $interval_days = 0;
    
    /**
     * Subscription interval, in months
     * 
     * Defaults to 0 if interval is not defined in months
     * 
     * @var int
     */
    public $interval_months = 0;
    
    /**
     * Subscription interval, in years
     * 
     * Defaults to 0 if interval is not defined in years
     * 
     * @var int
     */
    public $interval_years = 0;
    
    /**
     * Subscription trial period, in days
     * 
     * Defaults to 0 if there is no trial period
     * 
     * @var int
     */
    public $trial_days = 0;
    
    /**
     * Parses a JSON object into a Payment response
     * 
     * @param  mixed[]  $json
     * @return Payment
     */
    public static function parse($json)
    {
        $entity = new Subscription();
        $entity->id = isset($json['id']) ? $json['id'] : null;
        $entity->description = isset($json['description']) ? $json['description'] : null;
        $entity->name = isset($json['name']) ? $json['name'] : null;
        $entity->amount = isset($json['amount']) ? floatval($json['amount'])/100 : 0;
        $entity->interval_days = isset($json['interval_days']) ? intval($json['interval_days']) : 0;
        $entity->interval_months = isset($json['interval_months']) ? intval($json['interval_months']) : 0;
        $entity->interval_years = isset($json['interval_years']) ? intval($json['interval_years']) : 0;
        $entity->trial_days = isset($json['trial_days']) ? intval($json['trial_days']) : 0;

        return $entity;
    }
}