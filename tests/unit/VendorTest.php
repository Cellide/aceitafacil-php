<?php

namespace AceitaFacil\Tests\Unit;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class VendorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetVendorInfoMissing()
    {
        $client = new Client(true);
        $client->init('test', 'test');
        
        $response = $client->getVendor('');
    }
    
    public function testGetVendor()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"vendor":{
                                "id":"1234","name":"Acme","email":"acme@acme.com",
                                "bank":[
                                  {"code":"1","agency":"1111","account":"123123","account_type_id":1,"account_type":"CC",
                                   "account_holder_name":"Acme President","account_holder_document_type":1,
                                   "account_holder_document_number":"00000000001","verified":true},
                                   {"code":"2","agency":"2222","account":"456456","account_type_id":1,"account_type":"CC",
                                   "account_holder_name":"Acme President","account_holder_document_type":2,
                                   "account_holder_document_number":"00000000001","verified":false}
                                 ]}}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $response = $client->getVendor('1234');
        $this->assertFalse($response->isError(), 'Not an error');
        
        $vendors = $response->getObjects();
        $this->assertNotEmpty($vendors, 'Objects were filled');
        
        $vendor = $vendors[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $vendor, 'Object is a vendor');
        $this->assertNotEmpty($vendor->id, 'Id found');
        $this->assertNotEmpty($vendor->name, 'Name found');
        $this->assertNotEmpty($vendor->email, 'Email found');
        $this->assertNotEmpty($vendor->banks, 'Bank found');
            
        foreach ($vendor->banks as $bank) {
            $this->assertInstanceOf('AceitaFacil\Entity\Bank', $bank, 'Object is a Bank');
            $this->assertNotEmpty($bank->code, 'Code found');
            $this->assertNotEmpty($bank->agency, 'Agency found');
            $this->assertNotEmpty($bank->account_type, 'Account type found');
            $this->assertNotEmpty($bank->account_number, 'Account number found');
            $this->assertNotEmpty($bank->account_holder_name, 'Holder name found');
            $this->assertNotEmpty($bank->account_holder_document_type, 'Holder document type found');
            $this->assertNotEmpty($bank->account_holder_document_number, 'Holder document number found');
            $this->assertTrue(is_bool($bank->verified), 'Account verified status found');
        }
    }

    public function testCreateVendor()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"vendor":{
                                "id":"1234","name":"Acme","email":"acme@acme.com",
                                "bank":[
                                  {"code":"1","agency":"1111","account":"123123","account_type_id":1,"account_type":"CC",
                                   "account_holder_name":"Acme President","account_holder_document_type":1,
                                   "account_holder_document_number":"00000000001","verified":true}
                                 ]}}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $vendor = new Entity\Vendor();
        $vendor->id = 1;
        $vendor->name = 'Acme';
        $vendor->email = 'acme@acme.com';
        $bank = new Entity\Bank();
        $bank->code = "1";
        $bank->agency = "1111";
        $bank->account_type = "CC";
        $bank->account_number = "123123";
        $bank->account_holder_name = 'Acme President';
        $bank->account_holder_document_type = "CPF";
        $bank->account_holder_document_number = "00000000001";
        $vendor->banks[] = $bank;
        
        $response = $client->createVendor($vendor);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $vendors = $response->getObjects();
        $this->assertNotEmpty($vendors, 'Objects were filled');
        
        $vendor = $vendors[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $vendor, 'Object is a vendor');
        $this->assertNotEmpty($vendor->id, 'Id found');
        $this->assertNotEmpty($vendor->name, 'Name found');
        $this->assertNotEmpty($vendor->email, 'Email found');
        $this->assertNotEmpty($vendor->banks, 'Bank found');
            
        foreach ($vendor->banks as $bank) {
            $this->assertInstanceOf('AceitaFacil\Entity\Bank', $bank, 'Object is a Bank');
            $this->assertNotEmpty($bank->code, 'Code found');
            $this->assertNotEmpty($bank->agency, 'Agency found');
            $this->assertNotEmpty($bank->account_type, 'Account type found');
            $this->assertNotEmpty($bank->account_number, 'Account number found');
            $this->assertNotEmpty($bank->account_holder_name, 'Holder name found');
            $this->assertNotEmpty($bank->account_holder_document_type, 'Holder document type found');
            $this->assertNotEmpty($bank->account_holder_document_number, 'Holder document number found');
            $this->assertTrue(is_bool($bank->verified), 'Account verified status found');
        }
    }

    public function testUpdateVendor()
    {
        // mock API response
        $mock_adapter = new MockAdapter(function ($trans) {
            $factory = new MessageFactory();
            $response = $factory->createResponse(200, array(),
                            '{"vendor":{
                                "id":"1234","name":"Acme","email":"acme@acme.com",
                                "bank":[
                                  {"code":"1","agency":"1111","account":"123123","account_type_id":1,"account_type":"CC",
                                   "account_holder_name":"Acme Vice President","account_holder_document_type":1,
                                   "account_holder_document_number":"00000000002","verified":true}
                                 ]}}'
                        );
            return $response;
        });
        
        $client = new Client(true, $mock_adapter);
        $client->init('test', 'test');
        
        $vendor = new Entity\Vendor();
        $vendor->id = 1;
        $vendor->name = 'Acme';
        $vendor->email = 'acme@acme.com';
        $bank = new Entity\Bank();
        $bank->code = "1";
        $bank->agency = "1111";
        $bank->account_type = "CC";
        $bank->account_number = "123123";
        $bank->account_holder_name = 'Acme Vice President';
        $bank->account_holder_document_type = "CPF";
        $bank->account_holder_document_number = "00000000002";
        $vendor->banks[] = $bank;
        
        $response = $client->updateVendor($vendor);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $vendors = $response->getObjects();
        $this->assertNotEmpty($vendors, 'Objects were filled');
        
        $vendor = $vendors[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $vendor, 'Object is a vendor');
        $this->assertNotEmpty($vendor->id, 'Id found');
        $this->assertNotEmpty($vendor->name, 'Name found');
        $this->assertNotEmpty($vendor->email, 'Email found');
        $this->assertNotEmpty($vendor->banks, 'Bank found');
            
        foreach ($vendor->banks as $bank) {
            $this->assertInstanceOf('AceitaFacil\Entity\Bank', $bank, 'Object is a Bank');
            $this->assertNotEmpty($bank->code, 'Code found');
            $this->assertNotEmpty($bank->agency, 'Agency found');
            $this->assertNotEmpty($bank->account_type, 'Account type found');
            $this->assertNotEmpty($bank->account_number, 'Account number found');
            $this->assertNotEmpty($bank->account_holder_name, 'Holder name found');
            $this->assertNotEmpty($bank->account_holder_document_type, 'Holder document type found');
            $this->assertNotEmpty($bank->account_holder_document_number, 'Holder document number found');
            $this->assertTrue(is_bool($bank->verified), 'Account verified status found');
        }
    }
}