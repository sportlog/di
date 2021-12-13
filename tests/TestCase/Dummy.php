<?php

/**
 * Sportlog (https://sportlog.at)
 *
 * @license MIT License
 */
declare(strict_types = 1);

namespace Sportlog\Test\TestCase\YaMVC\DI;

final class Dummy implements DummyInterface {
    public function __construct(private string|array|null $optionalValue = null)
    {
    }

    public function getName(): string
    {
        return "dummy";
    }
}