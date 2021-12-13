<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase;

final class DummyRecursive {
    public function __construct(private DummyRecursive $dummy) {
    }
}