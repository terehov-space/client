# PHP client for Tarantool

[![Build Status](https://travis-ci.org/tarantool-php/client.svg?branch=master)](https://travis-ci.org/tarantool-php/client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tarantool-php/client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tarantool-php/client/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tarantool-php/client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tarantool-php/client/?branch=master)

This version of client requires Tarantool 1.7.1 or above.


## Installation

The recommended way to install the library is through [Composer](http://getcomposer.org):

```sh
composer require tarantool/client:@dev
```


## Usage

```php
use Tarantool\Client\Client;
use Tarantool\Client\Connection\Stream;
use Tarantool\Client\Packer\Pure;

$conn = new Stream();
// or
// $conn = new Stream('tcp://127.0.0.1:3301', [
//     'socket_timeout' => 5,
//     'connect_timeout' => 5,
//     'tcp_nodelay' => true,
// ]);
// or
// $conn = new Stream('unix:///tmp/tarantool_instance.sock');

$client = new Client($conn, new Pure());
// or
// $client = new Client($conn, new Pecl());

// if authentication credentials are required
// $client->authenticate('username', 'userpass');

$space = $client->getSpace('my_space');

// Selecting all data
$result = $space->select();
var_dump($result);

// Result: inserted tuple { 1, 'foo', 'bar' }
$space->insert([1, 'foo', 'bar']);

// Result: inserted tuple { 2, 'baz', 'qux' }
$space->upsert([2, 'baz', 'qux'], [['=', 1, 'BAZ'], ['=', 2, 'QUX']]);

// Result: updated tuple { 2, 'baz', 'qux' } with { 2, 'BAZ', 'QUX' }
$space->upsert([2, 'baz', 'qux'], [['=', 1, 'BAZ'], ['=', 2, 'QUX']]);

$result = $client->evaluate('return ...', 42);
var_dump($result);

$result = $client->call('box.stat');
var_dump($result);
```

> *Note*
>
> Using packer classes provided by the library require to install additional dependencies,
> which are not bundled with the library directly. Therefore, you have to install them manually.
> For example, if you plan to use `Packer\Pure`, install the [rybakit/msgpack](https://github.com/rybakit/msgpack.php#installation) package.
> See the "[suggest](composer.json#L20)" section of composer.json for other alternatives.


## Tests

To run unit tests:

```sh
phpunit --testsuite unit
```

To run integration tests:

```sh
phpunit --testsuite integration
```

> Make sure to start [client.lua](tests/Integration/client.lua) first.

To run all tests:

```sh
phpunit
```

If you already have Docker installed, you can run the tests in a docker container.
First, create a container:

```sh
./dockerfile.sh | docker build -t client -
```

The command above will create a container named `client` with PHP 7.3 runtime.
You may change the default runtime by defining the `IMAGE` environment variable:

```sh
IMAGE='php:7.2-cli' ./dockerfile.sh | docker build -t client -
```

> See a list of various images [here](.travis.yml#L8).


Then run Tarantool instance (needed for integration tests):

```sh
docker network create tarantool-php
docker run -d --net=tarantool-php -p 3301:3301 --name=tarantool -v `pwd`:/client \
    tarantool/tarantool:2 tarantool /client/tests/Integration/client.lua
```

And then run both unit and integration tests:

```sh
docker run --rm --net=tarantool-php --name client -v $(pwd):/client -w /client client
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
