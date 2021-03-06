[![Build Status](https://travis-ci.org/Cellide/aceitafacil-php.svg?branch=master)](https://travis-ci.org/Cellide/aceitafacil-php)
[![Coverage Status](https://img.shields.io/coveralls/Cellide/aceitafacil-php.svg)](https://coveralls.io/r/Cellide/aceitafacil-php)
[![Latest Stable Version](https://poser.pugx.org/cellide/aceitafacil-php/v/stable.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![Total Downloads](https://poser.pugx.org/cellide/aceitafacil-php/downloads.png)](https://packagist.org/packages/Cellide/aceitafacil-php)
[![License](https://poser.pugx.org/cellide/aceitafacil-php/license.svg)](https://packagist.org/packages/Cellide/aceitafacil-php)

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

Referência da API
-------------

```php

$client = new \AceitaFacil\Client($is_sandbox = false, $mock_adapter = null);

$client->init($username, $password);

$client->setPushEndpoint($url);

$client->saveCard(\AceitaFacil\Entity\Customer $customer, \AceitaFacil\Entity\Card $card);

$client->getAllCards($customer_id);

$client->deleteCard(\AceitaFacil\Entity\Customer $customer, $token);

$client->makePayment(\AceitaFacil\Entity\Customer $customer, $items, $description, \AceitaFacil\Entity\Card $card = null, $push_code = null);

$client->getPayment($payment_id);

$client->getVendor($vendor_id);

$client->createVendor(\AceitaFacil\Entity\Vendor $vendor);

$client->updateVendor(\AceitaFacil\Entity\Vendor $vendor);

$client->getPaymentItemInfo($payment_id, $item_id);

$client->updatePaymentItemInfo($payment_id, \AceitaFacil\Entity\Item $item);

$client->refund($payment_id);

$client->createSubscriptionPlan(\AceitaFacil\Entity\Subscription $subscription);

$client->updateSubscriptionPlan(\AceitaFacil\Entity\Subscription $subscription);

$client->getSubscriptionPlan($subscription_id);

$client->subscribe(\AceitaFacil\Entity\Customer $customer, $subscription_id, $description = null, \AceitaFacil\Entity\Card $card = null, $push_code = null);

$client->updateSubscribe(\AceitaFacil\Entity\Customer $customer, $subscription_id, $description = null, $push_code = null);

$client->getSubscribe($customer_id);

$client->cancelSubscribe($customer_id);

```

Changelog
-------------

- 1.4.0 (2014-08-22):
  - Adicionados métodos de assinatura
  - Nova entidade `Subscription`
  - Entidade `Payment` atualizada para cobrir mais informações sobre reembolso e assinaturas
  - Adicionados testes de unidade e integração 
  - Atualizado o README
  
- 1.3.0 (2014-08-21):
  - Adicionado método de reembolso
  - Adicionados testes de unidade e integração 
  - Atualizado o README

- 1.2.0 (2014-08-04):
  - Adicionadas opções de URL e código de callback para notificações de pagamentos
  - Push endpoint pode ser definido em `setPushEndpoint()`, mais de uma vez, se necessário uma URL por transação
  - Código único pode ser enviado em `makePayment()` como último (e opcional) parâmetro
  - Adicionados testes de unidade e integração 
  - Atualizado o README

- 1.1.0 (2014-06-27):
  - Refatorada assinatura de makePayment() para retirar redundância
  - Atualizado o README

- 1.0.0 (2014-06-26):
  - Primeira versão estável
  - 100% cobertura de testes
  - Testes de integração cobrem todos os casos de uso descritos na API pública (https://aceitafacil.com/docs)
  - Atualizado o README

- 0.9.2-beta (2014-06-26):
  - Todos os métodos da API pública estão disponíveis
  
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