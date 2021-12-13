<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase;

class Foo {
    public function __construct(private DummyInterface $dummy)
    {
    }
}