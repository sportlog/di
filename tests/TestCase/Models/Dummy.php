<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase\Models;

final class Dummy implements DummyInterface {
    public function __construct(private string|array|null $optionalValue = null)
    {
    }

    public function getName(): string
    {
        return "dummy";
    }
}