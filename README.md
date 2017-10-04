# Pushok

[![PHP >= 7.0](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status][ico-travis]][link-travis]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Coverage Status](https://coveralls.io/repos/github/edamov/pushok/badge.svg?branch=master)](https://coveralls.io/github/edamov/pushok?branch=master)
[![Quality Score][ico-code-quality]][link-code-quality]
[![Software License][ico-license]](LICENSE.md)

> **Note:** This library is under development

Pushok is a simple PHP library for sending push notifications to APNs. 

## Features

- [X] Uses new Apple APNs HTTP/2 connection
- [X] Supports JWT-based authentication
- [X] Supports new iOS 10 features such as Collapse IDs, Subtitles and Mutable Notifications
- [X] Uses concurrent requests to APNs
- [X] Tested and working in APNs production environment
- [ ] Supports Certificate-based authentication

## Requirements

* PHP >= 7.0
* lib-curl >= 7.46.0 (with http/2 support enabled)
* lib-openssl >= 1.0.2e 

Docker image that meets requirements can be found [here](https://hub.docker.com/r/edamov/pushok-docker).
Or you can follow [this tutorial](https://nathanleclaire.com/blog/2016/08/11/curl-with-http2-support---a-minimal-alpine-based-docker-image/) to create your own docker image with curl with HTTP/2 support.

## Install

Via Composer

``` bash
$ composer require edamov/pushok
```

## Getting Started

``` php
<?php
require __DIR__ . '/vendor/autoload.php';

use Pushok\AuthProvider;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;

$options = [
    'key_id' => 'AAAABBBBCC', // The Key ID obtained from Apple developer account
    'team_id' => 'DDDDEEEEFF', // The Team ID obtained from Apple developer account
    'app_bundle_id' => 'com.app.Test', // The bundle ID for app obtained from Apple developer account
    'private_key_path' => __DIR__ . '/private_key.p8', // Path to private key
    'private_key_secret' => null // Private key secret
];

$authProvider = AuthProvider\Token::create($options);

$alert = Alert::create()->setTitle('Hello!');
$alert = $alert->setBody('First push notification');

$payload = Payload::create()->setAlert($alert);

//set notification sound to default
$payload = $payload->setSound('default');

//add custom value to your notification, needs to be customized
$payload = $payload->setCustomValue('key', 'value');

$deviceTokens = ['<device_token_1>', '<device_token_2>', '<device_token_3>'];

$notifications = [];
foreach ($deviceTokens as $deviceToken) {
    $notifications[] = new Notification($payload,$deviceToken);
}

$client = new Client($authProvider, $production = false);
$client->addNotifications($notifications);



$responses = $client->push(); // returns an array of ApnsResponseInterface (one Response per Notification)

foreach ($responses as $devicetoken => $response) {
    $response->getApnsId();
    $response->getStatusCode();
    $response->getReasonPhrase();
    $response->getErrorReason();
    $response->getErrorDescription();
}
```

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email edamov@gmail.com instead of using the issue tracker.

## Credits

- [Arthur Edamov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/edamov/pushok.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/edamov/pushok/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/edamov/pushok.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/edamov/pushok.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/edamov/pushok.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/edamov/pushok
[link-travis]: https://travis-ci.org/edamov/pushok
[link-scrutinizer]: https://scrutinizer-ci.com/g/edamov/pushok/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/edamov/pushok
[link-downloads]: https://packagist.org/packages/edamov/pushok
[link-author]: https://github.com/pushok
[link-contributors]: ../../contributors
