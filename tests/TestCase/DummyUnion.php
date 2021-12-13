<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase;

final class DummyUnion {
    public function __construct(private FooInterface|DummyInterface $input) {
    }
}