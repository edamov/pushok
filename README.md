# apush

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

**Note:** This library is under development

## Install

Via Composer

``` bash
$ composer require edamov/apush
```

## Usage

``` php
$options = [
    'key_id' => 'AAAABBBBCC',
    'team_id' => 'DDDDEEEEFF',
    'app_bundle_id' => 'com.app.Test',
    'private_key_path' => __DIR__ . 'private_key.p8',
    'private_key_secret' => null
];

$authProvider = new AuthProvider\Token($options);

$payload = new Payload(); // create push notification body
$payload->setAlert('Hello!');

$deviceTokens = ['111', '222', '333'];
$messages = [];
foreach ($deviceTokens as $deviceToken) {
    $messages[] = new Message($deviceToken, $payload);
}
$client = new Client($authProvider, $production = false);
$client->addMessages($messages);

$response = $client->send();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email edamov@gmail.com instead of using the issue tracker.

## Credits

- [Arthur Edamov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/edamov/apush.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/edamov/apush/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/edamov/apush.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/edamov/apush.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/edamov/apush.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/edamov/apush
[link-travis]: https://travis-ci.org/edamov/apush
[link-scrutinizer]: https://scrutinizer-ci.com/g/edamov/apush/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/edamov/apush
[link-downloads]: https://packagist.org/packages/edamov/apush
[link-author]: https://github.com/edamov
[link-contributors]: ../../contributors
