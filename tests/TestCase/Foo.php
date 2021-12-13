<?php

/**
 * Sportlog (https://sportlog.at)
 *
 * @license MIT License
 */
declare(strict_types = 1);

namespace Sportlog\Test\TestCase\YaMVC\DI;

class Foo {
    public function __construct(private DummyInterface $dummy)
    {
    }
}