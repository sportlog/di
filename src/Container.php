<?php

declare(strict_types=1);

/**
 * A simple PSR-11 dependency injection container.
 *
 * @author Johannes Aberidis <jo@sportlog.at>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Sportlog\DI;

use Closure;
use Exception;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;
use Sportlog\DI\Exception\{ContainerException, NotFoundException};

/**
 * A simple PSR-11 dependency injection container.
 */
class Container implements ContainerInterface
{
    /**
     * Entries which have alread been resolved via get()
     * or where an instance was provided via set().
     */
    private array $resolvedEntries = [];
    /**
     * Definitions for all resolved types from Reflection.
     */
    private array $definitions = [];
    /**
     * Entries which are currently being resovlved. Required
     * to detect recursive dependencies.
     */
    private array $entriesBeingResolved = [];
    /**
     * Type mappings for non instantiable types
     * or factory function.
     *
     * @var (string|Closure)[]
     */
    private array $typeMapping = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     * Recursively calls this method for all parameters of the type.
     *
     * @param string $id Identifier of the entry to look for.
     * @throws NotFoundException No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     * @return mixed
     */
    public function get(string $id): mixed
    {
        $typeFactory = null;
        // Check for type mapping or factory function
        if (isset($this->typeMapping[$id])) {
            if (is_string($this->typeMapping[$id])) {
                $id = $this->typeMapping[$id];
            } else {
                $typeFactory = $this->typeMapping[$id];
            }
        }

        // If the entry is already resolved, return it
        if (isset($this->resolvedEntries[$id])) {
            return $this->resolvedEntries[$id];
        }

        if (isset($this->entriesBeingResolved[$id])) {
            throw new ContainerException("Circular dependency detected while trying to resolve entry '{$id}'");
        }

        try {
            $this->entriesBeingResolved[$id] = true;

            if ($typeFactory instanceof Closure) {
                $value = $this->instantiateFromFactory($typeFactory);
            } else {
                $value = $this->instantiate($id);
            }
            $this->resolvedEntries[$id] = $value;
            return $value;
        } finally {
            unset($this->entriesBeingResolved[$id]);
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     * @return bool
     */
    public function has(string $id): bool
    {
        // Check for type mapping or factory function
        if (isset($this->typeMapping[$id])) {
            if (is_string($this->typeMapping[$id])) {
                $id = $this->typeMapping[$id];
            } else {
                return true;
            }
        }

        return isset($this->resolvedEntries[$id]) || $this->getDefinition($id) !== false;
    }

    /**
     * Registers an instantiable type or object reference with an id.
     * Non instantiable types need to be manually set
     * via this method, otherwise DI::get() will throw an exception.
     *
     * @param string $id
     * @param string|Closure $value
     * @throws ContainerException
     */
    public function set(string $id, string|Closure $value): void
    {
        if (isset($this->resolvedEntries[$id])) {
            throw new ContainerException("Type '{$id}' is already resolved.");
        }

        if (isset($this->typeMapping[$id])) {
            throw new ContainerException("Type '{$id}' is already registered.");
        }

        $this->typeMapping[$id] = $value;
    }

    /**
     * Instantiates the type via the factory function.
     *
     * @param Closure $factory
     * @throws ContainerException Closure expects more arguments than the dependencies supply.
     * @return mixed
     */
    private function instantiateFromFactory(Closure $factory): mixed
    {
        $reflection = new ReflectionFunction($factory);
        $args = array_map(fn (ReflectionParameter $parameter) => $this->resolveParameter($parameter), $reflection->getParameters());
        return $factory->call($this, ...$args);
    }

    /**
     * Creates an instance of the provided type.
     *
     * @param string $id            The type to instantiate.
     * @throws NotFoundException    Type does not exist.
     * @throws ContainerException   Type is not instantiable.
     * @return object
     */
    private function instantiate(string $id): object
    {
        $reflection = $this->getDefinition($id);
        if ($reflection === false) {
            throw new NotFoundException("No entry or class found for '{$id}'");
        }
        if (!$reflection->isInstantiable()) {
            throw new ContainerException("Type '{$id}' is not instantiable." .
                " A type mapping or factory for this type must be manually provided via DI::set().");
        }

        $ctor = $reflection->getConstructor();
        if (is_null($ctor) || empty($ctor->getParameters())) {
            return $reflection->newInstance();
        }

        $args = array_map(fn (ReflectionParameter $parameter) => $this->resolveParameter($parameter), $ctor->getParameters());
        return $reflection->newInstanceArgs($args);
    }

    /**
     * Returns the type definition, or false if the type is not resolvable.
     *
     * @param string $id
     * @return bool|ReflectionClass
     */
    private function getDefinition(string $id): bool|ReflectionClass
    {
        if (isset($this->definitions[$id])) {
            return $this->definitions[$id];
        }

        try {
            $reflection = new ReflectionClass($id);
            $this->definitions[$id] = $reflection;
            return $reflection;
        } catch (ReflectionException) {
            return false;
        }
    }

    /**
     * Resolve parameters.
     *
     * @param ReflectionParameter $parameter
     * @throws Exception If the parameter is a primitive type and has no default value.
     * @return mixed
     */
    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            return $this->get($type->getName()); // Instantiate it
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException("Cannot resolve parameter '{$parameter->name}'");
    }
}
