[![Build Status](https://travis-ci.org/Cellide/aceitafacil-php.svg?branch=master)](https://travis-ci.org/Cellide/aceitafacil-php)
[![Coverage Status](https://img.shields.io/coveralls/Cellide/aceitafacil-php.svg)](https://coveralls.io/r/Cellide/aceitafacil-php)
[![Latest Stable Version](https://poser.pugx.org/Cellide/aceitafacil-php/v/stable.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![Total Downloads](https://poser.pugx.org/Cellide/aceitafacil-php/downloads.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![License](https://poser.pugx.org/Cellide/aceitafacil-php/license.svg)](https://packagist.org/packages/Cellide/aceitafacil-php)

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
$total_amount = $item1->amount + $item2->amount;

$response = $client->makePayment($customer, $description, $total_amount, $items, $card);
$response->isError(); // false
$response->getHttpStatus(); // 200

$receipts = $response->getObjects();
$receipt = $receipts[0];
$receipts->id; // transaction id

```

API Reference
-------------

```php
$client->init();

$client->saveCard();
$client->getAllCards();
$client->deleteCard();
$client->makePayment();
$client->getPayment();
```

Changelog
-------------

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