# lemon.markets php client

This repository contains a php 8+ compatible client for the https://lemon.markets API.
The documentation of the API can be found here https://docs.lemon.markets/api-endpoints/.

The API is not yet stable, therefore the client is subject to change too.

The client encapsules the basic operations from the API and maps the various
responses to strongly typed models.

## Installation

Since the API is changing quite frequently there won't be released versions.
For now, you need to depend on the master (or a specific commit).

```
composer require dfreudenberger/lemon-markets-client dev-master
```

## Usage

Make sure your composer dependencies are available to your code.

```php
require_once 'vendor/autoload.php';
```

Set up the `TokenClient`. The client performs the request against the 
authentication API to retrieve an access token, given your `client-id` 
and `client-secret`. On top of it you need to configure the `TokenCache`. 
Even though this wouldn't be necessary it will reduce the amount of 
authentication requests sent to a minimum.

```php
$tokenClient = new TokenClient('YOUR-CLIENT-ID', 'YOUR-CLIENT-SECRET');
$tokenCache = new TokenCache($tokenClient);
```

Last but not least, the client itself needs to be initiated.

```php
$client = new LemonMarketsClient($tokenCache);
```

### Examples

#### Place and activate an order

```php
<?php
require_once 'vendor/autoload.php';

$tokenClient = new TokenClient('YOUR-CLIENT-ID', 'YOUR-CLIENT-SECRET');
$tokenCache = new TokenCache($tokenClient);
$client = new LemonMarketsClient($tokenCache);

$placedOrder = $client->placeOrder(new PlaceOrderCommand(
    isin: 'US29786A1060',
    validUntil: strval(time() + 3600),
    side: PlaceOrderCommand::SIDE_BUY,
    quantity: 1
));
print_r($placedOrder);

$activation = $client->activateOrder($placedOrder->uuid);
print_r($activation);
```

### Running tests

Unit tests should exist for all critical parts of the code base. In order to run
the test suite, just execute the following command in the root directory of the 
repository.

```shell
./vendor/bin/phpunit
```

