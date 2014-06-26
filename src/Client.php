<?php

namespace AceitaFacil;

/**
 * Request wrapper for AceitaFácil's API
 * 
 * @author Fernando Piancastelli
 * @link https://github.com/Cellide/aceitafacil-php
 * @license MIT
 */
class Client
{
    /**
     * @var string
     */
    const SANDBOX_URL = 'https://sandbox.api.aceitafacil.com';
    
    /**
     * @var string
     */
    const PRODUCTION_URL = 'https://api.aceitafacil.com';
    
    /**
     * Guzzle client
     * 
     * @var GuzzleHttp\Client
     */
    private $client;
    
    /**
     * Username/App ID
     * 
     * @var string
     */
    private $username;
    
    /**
     * Password/App secret
     * 
     * @var string
     */
    private $password;
    
    /**
     * Client contructor
     * 
     * @param  bool   $is_sandbox     If true, sandobox environment is used. Default: false.
     * @param  mixed  $mock_adapter    Optionally use this \GuzzleHttp\Adapter\MockAdapter for requests
     * @return self
     */
    public function __construct($is_sandbox = false, $mock_adapter = null)
    {
        $options = (!empty($mock_adapter)) ?
                        array('adapter' => $mock_adapter) :
                        array('base_url' => ($is_sandbox ? self::SANDBOX_URL : self::PRODUCTION_URL));
        $this->client = new \GuzzleHttp\Client($options);
    }
    
    /**
     * Initializes the Client with a customer's username and password (public and private keys)
     * 
     * Must be called before any other method
     * 
     * @param  string    $username    Username (public key)
     * @param  string    $password    Password (private key)
     * @return void
     * @throws \InvalidArgumentException if not called correctly
     */
    public function init($username, $password)
    {
        if (empty($username) || empty($password))
            throw new \InvalidArgumentException('Username and password must be set');

        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Wrapper for API calls
     * 
     * @param  string  $method    HTTP method (POST, GET)
     * @param  string  $endpoint  API endpoint
     * @param  mixed[] $data      Data to be sent
     * @return Response
     * @throws \RuntimeException if not initialized
     */
    private function request($method, $endpoint, $data = null)
    {
        if (empty($this->username) || empty($this->password))
            throw new \RuntimeException('Client not properly initialized');
        
        $full_data = array(
            'auth' => array($this->username, $this->password),
            'body' => (!empty($data) ? $data : array())
        );
        $request = $this->client->createRequest($method, $endpoint, $full_data);
        
        $response = null;
        try {
            $response = $this->client->send($request);
        }
        catch (\GuzzleHttp\Exception\TransferException $e) {
            $response = $e->getResponse();
        }
        return new Response($response);
    }
    
    /**
     * Saves a Customer's Card info
     * 
     * Card must contain holder's name, number, and expiration date
     * 
     * @param  \AceitaFacil\Entity\Customer  $customer
     * @param  \AceitaFacil\Entity\Card      $card
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function saveCard(Entity\Customer $customer, Entity\Card $card)
    {
        if (empty($customer->id))
            throw new \InvalidArgumentException('Customer info missing');
        if (empty($card->name) || empty($card->number) || empty($card->exp_date))
            throw new \InvalidArgumentException('Card info missing');
        
        $data = array(
            'customer[id]' => $customer->id,
            'card[number]' => $card->number,
            'card[name]' => $card->name,
            'card[exp_date]' => $card->exp_date
        );
        return $this->request('POST', '/card', $data);
    }
    
    /**
     * Get all Cards stored for a Customer
     * 
     * @param  string       $customer_id
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function getAllCards($customer_id)
    {
        if (empty($customer_id))
            throw new \InvalidArgumentException('Customer info missing');
        
        return $this->request('GET', '/card?customer[id]='.$customer_id);
    }
    
    /**
     * Delete a stored Customer's Card
     * 
     * @param  \AceitaFacil\Entity\Customer  $customer
     * @param  string                        $token       Card's reference token
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function deleteCard(Entity\Customer $customer, $token)
    {
        if (empty($customer->id))
            throw new \InvalidArgumentException('Customer info missing');
        if (empty($token))
            throw new \InvalidArgumentException('Card token missing');
        
        $data = array(
            'customer[id]' => $customer->id,
            'card[token]' => $token
        );
        return $this->request('DELETE', '/card', $data);
    }
    
    /**
     * Make a payment using a card
     * 
     * Card must contain token and CVV
     * 
     * @param  Entity\Customer   $customer
     * @param  string            $description
     * @param  float             $total_amount
     * @param  Entity\Item[]     $items
     * @param  Entity\Card       $card           If not supplied, bill payment will be used
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function makePayment(Entity\Customer $customer, $description, $total_amount, $items, Entity\Card $card = null)
    {
        if (!empty($card) && (empty($card->token) || empty($card->cvv)))
            throw new \InvalidArgumentException('Card info missing');
        if (empty($customer) || empty($customer->id) || empty($customer->email) || empty($customer->name) || empty($customer->language))
            throw new \InvalidArgumentException('Customer info missing');
        if (empty($description))
            throw new \InvalidArgumentException('Description missing');
        if (empty($total_amount) || !is_numeric($total_amount) || floatval($total_amount) <= 0)
            throw new \InvalidArgumentException('Invalid amount');
        if (!is_array($items) || empty($items))
            throw new \InvalidArgumentException('Items missing');
        
        $total_amount = intval(floatval($total_amount)*100);
        $payment_method = (!empty($card)) ? 1 : 2;
        
        $data = array(
            'description' => $description,
            'paymentmethod[id]' => $payment_method,
            'total_amount' => $total_amount,
            
            'customer[id]' => $customer->id,
            'customer[email]' => $customer->email,
            'customer[name]' => $customer->name,
            'customer[email_language]' => $customer->language
        );
        if ($payment_method === 1) {
            $card_data = array(
                'card[token]' => $card->token,
                'card[cvv]' => $card->cvv,
            );
            $data = array_merge($data, $card_data);
        }
        
        for ($i = 0; $i < count($items); $i++) {
            $item_data = array();
            $item_amount = intval(floatval($items[$i]->amount)*100);
            $item_data["item[$i][amount]"] = $item_amount;
            $item_data["item[$i][vendor_id]"] = $items[$i]->vendor->id;
            $item_data["item[$i][vendor_name]"] = $items[$i]->vendor->name;
            $item_data["item[$i][fee_split]"] = $items[$i]->fee_split;
            $item_data["item[$i][description]"] = $items[$i]->description;
            $item_data["item[$i][trigger_lock]"] = ($items[$i]->trigger_lock == true) ? 1 : 0;
            $data = array_merge($data, $item_data);
        }
        
        return $this->request('POST', '/payment', $data);
    }

    /**
     * Get info about a payment
     * 
     * @param  string    $payment_id    A Payment transaction ID returned by {makePayment}
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function getPayment($payment_id)
    {
        if (empty($payment_id))
            throw new \InvalidArgumentException('Payment info missing');
        
        return $this->request('GET', '/payment?invoice[id]='.$payment_id);
    }
    
    /**
     * Get info about a vendor
     * 
     * @param  string    $vendor_id    A vendor ID
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function getVendor($vendor_id)
    {
        if (empty($vendor_id))
            throw new \InvalidArgumentException('Payment info missing');
        
        return $this->request('GET', '/vendor?vendor[id]='.$vendor_id);
    }
    
    /**
     * Create a Vendor
     * 
     * If Vendor contains a Bank, the first (and only the first)
     * will be saved as well
     * 
     * @param  \AceitaFacil\Entity\Vendor    $vendor
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function createVendor(Entity\Vendor $vendor)
    {
        return $this->upsertVendor($vendor, 'POST');
    }
    
    /**
     * Update a Vendor info
     * 
     * If Vendor contains a Bank, the first (and only the first)
     * will be saved as well
     * 
     * @param  \AceitaFacil\Entity\Vendor    $vendor
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function updateVendor(Entity\Vendor $vendor)
    {
        return $this->upsertVendor($vendor, 'PUT');
    }
    
    /**
     * Upsert a Vendor info
     * 
     * If Vendor contains a Bank, the first (and only the first)
     * will be saved as well
     * 
     * @param  \AceitaFacil\Entity\Vendor    $vendor
     * @param  string                        $method   POST for insert, PUT for update
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    private function upsertVendor(Entity\Vendor $vendor, $method)
    {
        if (empty($vendor->id) || empty($vendor->email) || empty($vendor->name))
            throw new \InvalidArgumentException('Customer info missing');
        if (empty($method) || ($method != 'POST' && $method != 'PUT'))
            throw new \InvalidArgumentException('Request method missing');
        
        $data = array(
            'vendor[id]' => $vendor->id,
            'vendor[name]' => $vendor->name,
            'vendor[email]' => $vendor->email
        );
        
        if (!empty($vendor->banks)) {
            $bank = $vendor->banks[0];
            $bank_data = array(
                'vendor[bank][code]' => $bank->code,
                'vendor[bank][agency]' => $bank->agency,
                'vendor[bank][account_type]' => ($bank->account_type == 'CC') ? 1 : 2,
                'vendor[bank][account_number]' => $bank->account_number,
                'vendor[bank][account_holder_name]' => $bank->account_holder_name,
                'vendor[bank][account_holder_document_type]' => ($bank->account_holder_document_type == 'CPF') ? 1 : 2,
                'vendor[bank][account_holder_document_number]' => $bank->account_holder_document_number,
            );
            $data = array_merge($data, $bank_data);
        }
        return $this->request($method, '/vendor', $data);
    }

    /**
     * Get info about an Item in an invoice
     * 
     * @param  string    $payment_id    A payment transaction ID
     * @param  string    $item_id       An Item ID
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function getPaymentItemInfo($payment_id, $item_id)
    {
        if (empty($item_id) || empty($payment_id))
            throw new \InvalidArgumentException('Payment info missing');
        
        return $this->request('GET', "/invoice/$payment_id/item/$item_id");
    }

    /**
     * Update an Item info in an invoice
     * 
     * The only updateable info is an Item's vendor ID and trigger_lock,
     * everything else is ignored
     * 
     * @param  string                    $payment_id    A payment transaction ID
     * @param  \AceitaFacil\Entity\Item  $item          Item to be changed
     * @return Response
     * @throws \InvalidArgumentException if not called correctly
     */
    public function updatePaymentItemInfo($payment_id, Entity\Item $item)
    {
        if (empty($payment_id))
            throw new \InvalidArgumentException('Payment info missing');
        if (empty($item->vendor) || empty($item->vendor->id) || !is_bool($item->trigger_lock))
            throw new \InvalidArgumentException('Item info missing');
        
        $data = array(
            'item[vendor_id]' => $item->vendor->id,
            'item[trigger_lock]' => ($item->trigger_lock == true) ? 1 : 0
        );
        
        return $this->request('PUT', "/invoice/$payment_id/item/$item_id", $data);
    }
}
