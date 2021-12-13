# DI
A simple PSR-11 dependency injection container.

## Install via Composer
You can install sportlog/di using Composer. (coming soon)

``` bash
$ composer require sportlog/di
```
Minimum PHP version required is 8.

## How to use

``` php
<?php

require 'vendor/autoload.php';

use Sportlog\DI\Container;

// Given this class and interface:
interface FooInterface {
    public function getFoo(): string;
}

class Foo implements FooInterface {
    public function __construct(private ?string $foo = 'foo') {
    }

    public function getFoo(): string {
        return $this->foo;
    }
}

// 1) You can simply get the instance via class id
$container = new Container();
$foo = $container->get(Foo::class);

// 2) When working with interfaces you must set
// the class id which shall be created
$container = new Container();
$container->set(FooInterface::class, Foo::class);
$foo = $container->get(FooInterface::class);


// 3) You can also use a factory with an optional
// set of dependencies if required.
class Config {
    public function getFooInit(): string {
        return 'init-foo';
    }
}

$container = new Container();
$container->set(FooInterface::class, fn(Config $config) => new Foo($config->getFooInit()), [Config::class]);
$foo = $container->get(FooInterface::class);
```
