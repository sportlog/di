<?php

/**
 * Sportlog (https://sportlog.at)
 *
 * @license MIT License
 */
declare(strict_types = 1);

namespace Sportlog\Test\TestCase\YaMVC\DI;

final class DummyUnion {
    public function __construct(private FooInterface|DummyInterface $input) {
    }
}