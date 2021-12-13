<?php

/**
 * DI
 *
 * @license MIT License
 */

declare (strict_types = 1);

namespace Sportlog\DI;

use Closure;

/**
 * Undocumented class
 */
class FactoryDefinition {
    public function __construct(private Closure $factory, private array $deps = [])
    {
    }

    public function getFactory(): Closure {
        return $this->factory;
    }

    public function getDependencies(): array {
        return $this->deps;
    }
}