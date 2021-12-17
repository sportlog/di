<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase\Models;

class Foo implements FooInterface {
    public function __construct(private DummyInterface $dummy)
    {
    }

    public function getFoo(): string {
        return 'foo';
    }
}