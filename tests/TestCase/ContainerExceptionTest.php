<?php

declare(strict_types=1);

namespace Sportlog\DI\Test\TestCase;

use ArgumentCountError;
use PHPUnit\Framework\TestCase;
use Sportlog\DI\Container;
use Sportlog\DI\Exception\NotFoundException;
use Sportlog\DI\Exception\ContainerException;
use Sportlog\DI\Test\TestCase\Models\Dummy;
use Sportlog\DI\Test\TestCase\Models\DummyInterface;
use Sportlog\DI\Test\TestCase\Models\DummyRecursive;
use Sportlog\DI\Test\TestCase\Models\Foo;

final class ContainerExceptionTest extends TestCase
{
    /**
     * Creating a class with mandatory union-type must throw
     *
     * @return void
     */
    public function testNotNullableParameterThrows(): void
    {
        $this->expectException(ContainerException::class);

        $di = new Container();
        $di->get(Foo::class);
    }

    /**
     * Creating an non-registered type must throw
     *
     * @return void
     */
    public function testUnregisteredNotInstantiableTypeThrows(): void
    {
        $this->expectException(ContainerException::class);

        $di = new Container();
        $di->get(DummyInterface::class); // must throw as DummyInterface is not registered
    }

    /**
     * If a mapping for a type already exists an exception is thrown
     *
     * @return void
     */
    public function testMappingExistsThrows(): void
    {
        $this->expectException(ContainerException::class);

        $di = new Container();
        $di->set(DummyInterface::class, Dummy::class);
        $dummy = $di->get(DummyInterface::class);
        $this->assertIsObject($dummy);

        $di->set(DummyInterface::class, 'alreadyRegistered');
    }

    /**
     * If a mapping for a type already exists an exception is thrown
     *
     * @return void
     */
    public function testMappingWhenEntryResolvedThrows(): void
    {
        $this->expectException(ContainerException::class);

        $di = new Container();
        $di->set(DummyInterface::class, Dummy::class);
        $di->set(DummyInterface::class, 'alreadyRegistered');
    }

    /**
     * Creating an invalid class must throw
     *
     * @return void
     */
    public function testInvalidClassThrows(): void
    {
        $this->expectException(NotFoundException::class);

        $di = new Container();
        $di->get("dummy"); // some invalid class
    }

    /**
     * Creating a recursive type must throw
     *
     * @return void
     */
    public function testGetRecursiveTypeThrows(): void
    {
        $this->expectException(ContainerException::class);

        $di = new Container();
        $di->get(DummyRecursive::class);
    }
}
