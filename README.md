# Guzzle Snapshots Middleware

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Guzzle 6 middleware to store responses and requests and replay them in tests.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/        
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require emanueleminotto/guzzle-snapshot
```

## Usage

```php
use EmanueleMinotto\GuzzleSnapshot\Strategy\ReadableFileStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
// use Doctrine\Common\Cache\ArrayCache;
// use EmanueleMinotto\GuzzleSnapshot\Strategy\DoctrineCacheStrategy;

// $strategy = new DoctrineCacheStrategy(new ArrayCache());
$strategy = new ReadableFileStrategy(__DIR__.'/__snapshots__');
$middleware = new SnapshotMiddleware($strategy);
// $middleware->setRequestStorage(true); // this will save requests too

$stack = HandlerStack::create();
$stack->push($middleware);

$client = new Client([
    'handler' => $this->stack,
]);

// if the response file was already stored in
// the directory __snapshots___, this request wont
// be sent to the remote endpoint
$client->get('http://www.example.com/');

// this request wont be sent in any case
$client->get('http://www.example.com/');
```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email minottoemanuele@gmail.com instead of using the issue tracker.

## Credits

- [Emanuele Minotto][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/emanueleminotto/guzzle-snapshot.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/EmanueleMinotto/guzzle-snapshot/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/EmanueleMinotto/guzzle-snapshot.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/EmanueleMinotto/guzzle-snapshot.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/emanueleminotto/guzzle-snapshot.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/emanueleminotto/guzzle-snapshot
[link-travis]: https://travis-ci.org/EmanueleMinotto/guzzle-snapshot
[link-scrutinizer]: https://scrutinizer-ci.com/g/EmanueleMinotto/guzzle-snapshot/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/EmanueleMinotto/guzzle-snapshot
[link-downloads]: https://packagist.org/packages/emanueleminotto/guzzle-snapshot
[link-author]: https://github.com/EmanueleMinotto
[link-contributors]: ../../contributors
