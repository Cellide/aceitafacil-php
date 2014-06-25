<?php

namespace AceitaFacil\Entity;

/**
 * Bank account information
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Bank
{
    /**
     * Bank code
     * 
     * @var string
     */
    public $code;
    
    /**
     * Bank agency where account was created
     * 
     * @var string
     */
    public $agency;
    
    /**
     * Account type
     * 
     * @var string
     */
    public $account_type;
    
    /**
     * Account number
     * 
     * @var string
     */
    public $account_number;
    
    /**
     * Account holder name
     * 
     * @var string
     */
    public $account_holder_name;
    
    /**
     * Account holder document type (CPF, CNPJ)
     * 
     * @var string
     */
    public $account_holder_document_type;
    
    /**
     * Account holder document number
     * 
     * @var string
     */
    public $account_holder_document_number;
    
    /**
     * If the account information has been verified
     * 
     * @var bool
     */
    public $verified;
    
    /**
     * Parses a JSON object into a Bank
     * 
     * @param  mixed[]  $json
     * @return Bank
     */
    public static function parse($json)
    {
        $entity = new Bank();
        $entity->code = isset($json['code']) ? $json['code'] : null;
        $entity->agency = isset($json['agency']) ? $json['agency'] : null;
        $entity->account_number = isset($json['account']) ? $json['account'] : null;
        $entity->account_type = isset($json['account_type']) ? $json['account_type'] : null;
        $entity->account_holder_name = isset($json['account_holder_name']) ? $json['account_holder_name'] : null;
        $entity->account_holder_document_number = isset($json['account_holder_document_number']) ? $json['account_holder_document_number'] : null;
        $entity->verified = isset($json['verified']) ? ($json['verified'] == true) : false; 
        
        $account_holder_document_type = isset($json['account_holder_document_type']) ? $json['account_holder_document_type'] : null;
        switch ($account_holder_document_type) {
            case 1:
                $entity->account_holder_document_type = 'CPF';
                break;
            case 2:
                $entity->account_holder_document_type = 'CNPJ';
                break;
        }
        
        return $entity;
    }
}