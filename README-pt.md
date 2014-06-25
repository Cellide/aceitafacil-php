[![Build Status](https://travis-ci.org/Cellide/aceitafacil-php.svg?branch=master)](https://travis-ci.org/Cellide/aceitafacil-php)
[![Coverage Status](https://img.shields.io/coveralls/Cellide/aceitafacil-php.svg)](https://coveralls.io/r/Cellide/aceitafacil-php)
[![Latest Stable Version](https://poser.pugx.org/Cellide/aceitafacil-php/v/stable.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![Total Downloads](https://poser.pugx.org/Cellide/aceitafacil-php/downloads.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![License](https://poser.pugx.org/Cellide/aceitafacil-php/license.svg)](https://packagist.org/packages/Cellide/aceitafacil-php)

Select Language: [English](../master/README.md), **Portuguese**

aceitafacil-php
===================

Uma SDK em PHP para o sistema de pagamentos [AceitaFacil](https://aceitafacil.com)

Instalação
-------------

A maneira mais rápida é pelo Composer:

```bash
$> composer install cellide/aceitafacil-php
```

Ou adicionando como uma dependência ao seu `composer.json`:

```composer.json
{
    "require": {
		...
		"cellide/aceitafacil-php": "*"
	}
}
```

Uso
-------------

O objecto `Client` pode fazer uma requisição a qualquer método da API pública do AceitaFacil. Todos eles retornam um objeto `Response` contendo informações gerais sobre a resposta (erro ou não, http statuso code), e as entidades concretas decodificados do corpo da resposta:

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

Referência da API
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
  - Disponível no [Packagist](https://packagist.org/packages/cellide/aceitafacil-php)
  - Novas badges
  - Travis build faz testes de unidade e integração
  
- 0.9.0-beta (2014-06-25):
  - Versão beta
  - Nem todos os métodos estão disponíveis ainda
  - Integração com Travis em https://travis-ci.org/Cellide/aceitafacil-php

License
-------------
MIT, seguindo a licença do próprio PHP SDK disponibilizado pelo AceitaFacil: https://github.com/aceitaFacil/aceitaFacil-php