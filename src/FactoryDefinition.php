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
 * Defines the Closure and it's dependencies to instantiate a type.
 */
class FactoryDefinition {
    public function __construct(private Closure $factory, private array $deps = [])
    {
    }

    /**
     * The factory to create a type.
     *
     * @return Closure
     */
    public function getFactory(): Closure {
        return $this->factory;
    }

    /**
     * List of class ids to be injected into the Closure.
     *
     * @return array
     */
    public function getDependencies(): array {
        return $this->deps;
    }
}