<?php

namespace AceitaFacil\Entity;

/**
 * Purchaseable item information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Item
{
    /**
     * Item ID
     * 
     * Returned by API when a payment is made
     * 
     * @var string
     */
    public $id;
    
    /**
     * Value amount
     * 
     * @var float
     */
    public $amount;
    
    /**
     * Description
     * 
     * @var string
     */
    public $description;
    
    /**
     * Vendor
     * 
     * @var Vendor
     */
    public $vendor;
    
    /**
     * Fee split
     * 
     * @link http://aceitafacil.com/calculadora/
     * 
     * @var int
     */
    public $fee_split;
    
    /**
     * Trigger Lock
     * 
     * If true, payment to vendor from this item won't be released
     * until set as false by an API call
     * 
     * @var bool
     */
    public $trigger_lock = false;
    
    /**
     * Parses a JSON object into an Item
     * 
     * @param  mixed[]  $json
     * @return Item
     */
    public static function parse($json)
    {
        $entity = new Item();
        $entity->id = isset($json['id']) ? $json['id'] : null;
        $entity->description = isset($json['description']) ? $json['description'] : null;
        $entity->amount = isset($json['amount']) ? (intval($json['amount'])/100) : null;
        $entity->fee_split = isset($json['fee_split']) ? $json['fee_split'] : null;
        $entity->trigger_lock = isset($json['trigger_lock']) ? ($json['trigger_lock'] == true) : false;
        
        $entity->vendor = new Vendor();
        $entity->vendor->id = isset($json['vendor_id']) ? $json['vendor_id'] : null;
        $entity->vendor->name = isset($json['vendor_name']) ? $json['vendor_name'] : null;
        
        return $entity;
    }
}