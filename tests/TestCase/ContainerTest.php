<?php

declare(strict_types=1);

namespace Sportlog\DI\Test\TestCase;

use Closure;
use PHPUnit\Framework\TestCase;
use Sportlog\DI\Container;
use Sportlog\DI\Exception\ContainerException;
use Sportlog\DI\Test\TestCase\Models\Dummy;
use Sportlog\DI\Test\TestCase\Models\DummyInterface;
use Sportlog\DI\Test\TestCase\Models\DummyUser;
use Sportlog\DI\Test\TestCase\Models\{Foo, FooInterface};

final class ContainerTest extends TestCase
{

    public function testGetValidClass(): void
    {
        $di = new Container();
        $instance = $di->get(DummyUser::class);
        $this->assertIsObject($instance);
    }

    public function testMappingFactoryGetValidClass(): void
    {
        $di = new Container();

        $b = new Dummy();
        $di->set(DummyInterface::class, fn() => $b);
        $this->assertTrue($di->has(DummyInterface::class));
        $this->assertSame($b, $di->get(DummyInterface::class));

        $b2 = $di->get(DummyUser::class);
        $this->assertIsObject($b2);
        // as $b was create via factory those instances cannot be identical
        $this->assertNotSame($b, $b2);
    }

    public function testMappingFactoryWithArgsGetValidClass(): void
    {
        $di = new Container();

        $di->set(Foo::class, fn(Dummy $dummy): Foo => new Foo($dummy));
        $instance = $di->get(Foo::class);
        $this->assertIsObject($instance);
    }

    public function testMappingFactoryString(): void
    {
        $di = new Container();

        $di->set('foo', fn()  => 'some string');
        $has = $di->has('foo');
        $this->assertTrue($has);
        $this->assertEquals('some string', $di->get('foo'));
    }

    public function testMappingSameInstance(): void
    {
        $di = new Container();

        $di->set(DummyInterface::class, Dummy::class);
        $instance = $di->get(DummyInterface::class);
        $this->assertInstanceOf(DummyInterface::class, $instance);

        // Qerying again should return the same instance, regardless of querying via interface or class
        $instance2 = $di->get(DummyInterface::class);
        $this->assertSame($instance2, $instance);

        $instance3 = $di->get(Dummy::class);
        $this->assertSame($instance3, $instance);
    }

    public function testMappingGetValidClass(): void
    {
        $di = new Container();
        $di->set(DummyInterface::class, Dummy::class);
        $foo = $di->get(Foo::class);
        $this->assertIsObject($foo);
    }

    public function testGetValidFromFactory(): void
    {
        $di = new Container();

        $di->set(DummyInterface::class, Dummy::class);
        $di->set(Foo::class, fn(DummyInterface $dummy) => new Foo($dummy), [DummyInterface::class]);

        $foo = $di->get(Foo::class);
        $this->assertIsObject($foo);
        $this->assertEquals('foo', $foo->getFoo());
    }

    public function testHasValidClass(): void
    {
        $di = new Container();
        $has = $di->has(Foo::class);
        $this->assertTrue($has);
    }

    public function testHasInValidClass(): void
    {
        $di = new Container();
        $has = $di->has('SomeClass');
        $this->assertFalse($has);
    }

    public function testCreateUnmappedClassThrows(): void
    {
        $di = new Container();

        $this->expectException(ContainerException::class);
        $di->get(FooInterface::class);
    }

    public function testHasTypeMappedClass(): void
    {
        $di = new Container();

        $has = $di->has(FooInterface::class);
        $this->assertTrue($has);

        $di->set(FooInterface::class, Foo::class);
        $has = $di->has(FooInterface::class);
        $this->assertTrue($has);
    }

    public function testSettingMappingIdAfterResolveThrows(): void
    {
        $di = new Container();
        $di->get(Dummy::class);

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Type '" . Dummy::class . "' is already resolved.");
        $di->set(Dummy::class, Closure::fromCallable(fn() => new Dummy()));
    }

    public function testSettingChaningMappingIdAfterResolveThrows(): void
    {
        $di = new Container();
        $di->set(DummyInterface::class, Dummy::class);
        $di->set(FooInterface::class, Foo::class);
        $di->get(FooInterface::class);

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Type '" . FooInterface::class . "' is already registered.");
        $di->set(FooInterface::class, Foo::class);
    }
}
