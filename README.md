[![Build Status](https://travis-ci.org/Cellide/aceitafacil-php.svg?branch=master)](https://travis-ci.org/Cellide/aceitafacil-php)
[![Coverage Status](https://img.shields.io/coveralls/Cellide/aceitafacil-php.svg)](https://coveralls.io/r/Cellide/aceitafacil-php)
[![Latest Stable Version](https://poser.pugx.org/cellide/aceitafacil-php/v/stable.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![Total Downloads](https://poser.pugx.org/cellide/aceitafacil-php/downloads.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![License](https://poser.pugx.org/cellide/aceitafacil-php/license.svg)](https://packagist.org/packages/Cellide/aceitafacil-php)

Select Language: **English**, [Portuguese](../master/README-pt.md)

aceitafacil-php
===================

An [AceitaFacil](https://aceitafacil.com) SDK for PHP

Install
-------------

Fastest way is to install via Composer:

```bash
$> composer install cellide/aceitafacil-php
```

Or by adding it to your composer.json dependencies:

```composer.json
{
    "require": {
		...
		"cellide/aceitafacil-php": "*"
	}
}
```

Usage
-------------

The `Client` object can request everything from the AceitaFacil remote API using its methods. They all return a `Response` object containg general info about the response (error or not, http status code), and concrete entities from the parsed response body:

```php
// start the client
$client = new AceitaFacil\Client();
$client->init('your id', 'your secret');

// set a push endpoint
$client->setPushEndpoint('https://acme.com/endpoint');

// create a customer for your products
$customer = new AceitaFacil\Entity\Customer();
$customer->id = 1;
$customer->name = 'John Doe';
$customer->email = 'johndoe@acme.com';
$customer->language = 'EN';

// create a card for this customer
$card = new AceitaFacil\Entity\Card();
$card->name = 'John Doe';
$card->number = '1111111111111111';
$card->exp_date = '205005'; // YYYYMM

// save the card
$response = $client->saveCard($customer, $card);
$response->isError(); // false
$response->getHttpStatus(); // 200

$cards = $response->getObjects();
$card = $cards[0];
$token = $card->token; // token referencing the saved card

// create your vendor basic information for payments
$vendor = new AceitaFacil\Entity\Vendor();
$vendor->id = 'your vendor id';
$vendor->name = 'Acme';

// create your items purchased array
$items = array();

$item1 = new AceitaFacil\Entity\Item();
$item1->id = 10;
$item1->description = 'Razor blade';
$item1->amount = 4.99;
$item1->vendor = $vendor; // referencing you
$item1->fee_split = 1;
$item1->trigger_lock = false;

$item2 = new AceitaFacil\Entity\Item();
$item2->id = 11;
$item2->description = 'Band aid';
$item2->amount = 1.99;
$item2->vendor = $vendor;
$item2->fee_split = 1;
$item2->trigger_lock = false;

$items[] = $item1;
$items[] = $item2;

$description = 'Random purchase';

$card->cvv = '111'; // include the CVV in a card for payments

$response = $client->makePayment($customer, $items, $description, $card);
$response->isError(); // false
$response->getHttpStatus(); // 200

$receipts = $response->getObjects();
$receipt = $receipts[0];
$receipts->id; // transaction id

```

API Reference
-------------

```php

$client = new \AceitaFacil\Client($is_sandbox = false, $mock_adapter = null);

$client->init($username, $password);

$client->setPushEndpoint($url);

$client->saveCard(\AceitaFacil\Entity\Customer $customer, \AceitaFacil\Entity\Card $card);

$client->getAllCards($customer_id);

$client->deleteCard(\AceitaFacil\Entity\Customer $customer, $token);

$client->makePayment(\AceitaFacil\Entity\Entity\Customer $customer, $items, $description, \AceitaFacil\Entity\Entity\Card $card = null, $push_code = null);

$client->getPayment($payment_id);

$client->getVendor($vendor_id);

$client->createVendor(\AceitaFacil\Entity\Entity\Vendor $vendor);

$client->updateVendor(\AceitaFacil\Entity\Entity\Vendor $vendor);

$client->getPaymentItemInfo($payment_id, $item_id);

$client->updatePaymentItemInfo($payment_id, \AceitaFacil\Entity\Entity\Item $item);

$client->refund($payment_id);

```

Changelog
-------------

- 1.3.0 (2014-08-21):
  - Added refund method
  - Added unit & integration tests
  - Updated README

- 1.2.0 (2014-08-04):
  - Added callback URL and code options for payment notifications
  - Push endpoint can be set using `setPushEndpoint()`, multiple times if needed
  - Unique code can be sent along with `makePayment()` as last, optional parameter
  - Added unit & integration tests
  - Updated README
  
- 1.1.0 (2014-06-27):
  - Refactored makePayment() signature to remove redudancy
  - Updated README

- 1.0.0 (2014-06-26):
  - First stable version
  - 100% test coverage
  - Integration tests describe all use cases from the public API (https://aceitafacil.com/docs)
  - Updated README

- 0.9.2-beta (2014-06-26):
  - All API methods encompassed
  
- 0.9.1-beta (2014-06-25):
  - Available on [Packagist](https://packagist.org/packages/cellide/aceitafacil-php)
  - New badges
  - Travis build does unit and integration testing
  
- 0.9.0-beta (2014-06-25):
  - Beta version
  - Not all API methods available yet
  - Travis integration at https://travis-ci.org/Cellide/aceitafacil-php

License
-------------
MIT, following on AceitaFacil's own PHP SDK: https://github.com/aceitaFacil/aceitaFacil-php