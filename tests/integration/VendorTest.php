<?php

namespace AceitaFacil\Tests\Integration;

use AceitaFacil\Client,
    AceitaFacil\Entity,
    GuzzleHttp\Adapter\MockAdapter,
    GuzzleHttp\Message\Response,
    GuzzleHttp\Message\MessageFactory;


class VendorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateVendor()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $vendor = new Entity\Vendor();
        $vendor->id = time();
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
        
        return $vendor;
    }

    /**
     * @depends testCreateVendor
     */
    public function testGetVendor($original_vendor)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $response = $client->getVendor($original_vendor->id);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $vendors = $response->getObjects();
        $this->assertNotEmpty($vendors, 'Objects were filled');
        
        $vendor = $vendors[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $vendor, 'Object is a vendor');
        $this->assertNotEmpty($vendor->id, 'Id found');
        $this->assertEquals($original_vendor->id, $vendor->id, 'Original and found vendors ID match');
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
        
        return $vendor;
    }

    /**
     * @depends testGetVendor
     */
    public function testCreateSameVendor($original_vendor)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $vendor = new Entity\Vendor();
        $vendor->id = $original_vendor->id;
        $vendor->name = 'Acme 2';
        $vendor->email = 'acme2@acme.com';
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
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(409, $response->getHttpStatus(), 'HTTP status 409 returned');
    }
    
    /**
     * @depends testGetVendor
     */
    public function testUpdateVendor($original_vendor)
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $vendor = $original_vendor;
        $vendor->name = 'Acme 2';
        $vendor->email = 'acme2@acme.com';
        
        $response = $client->updateVendor($vendor);
        $this->assertFalse($response->isError(), 'Not an error');
        
        $vendors = $response->getObjects();
        $this->assertNotEmpty($vendors, 'Objects were filled');
        
        $vendor = $vendors[0];
        $this->assertInstanceOf('AceitaFacil\Entity\Vendor', $vendor, 'Object is a vendor');
        $this->assertNotEmpty($vendor->id, 'Id found');
        $this->assertEquals($original_vendor->id, $vendor->id, 'Original and found vendors ID match');
        $this->assertNotEmpty($vendor->name, 'Name found');
        $this->assertEquals('Acme 2', $vendor->name, 'Vendor name changed to Acme 2');
        $this->assertNotEmpty($vendor->email, 'Name found');
        $this->assertEquals('acme2@acme.com', $vendor->email, 'Vendor email changed to acme2@acme.com');
    }

    public function testUpdateInexistentVendor()
    {
        $client = new Client(true);
        $client->init(getenv('APPID'), getenv('APPSECRET'));
        
        $vendor = new Entity\Vendor();
        $vendor->id = 'asdfasdfasdf';
        $vendor->name = 'Acme 3';
        $vendor->email = 'acme3@acme.com';
        
        $response = $client->updateVendor($vendor);
        $this->assertTrue($response->isError(), 'Is an error');
        $this->assertEquals(404, $response->getHttpStatus(), 'HTTP status 404 returned');
    }
}