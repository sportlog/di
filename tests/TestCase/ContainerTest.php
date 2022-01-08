<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase;

use FooInterface;
use PHPUnit\Framework\TestCase;
use Sportlog\DI\Container;
use Sportlog\DI\Test\TestCase\Models\Dummy;
use Sportlog\DI\Test\TestCase\Models\DummyInterface;
use Sportlog\DI\Test\TestCase\Models\DummyUser;
use Sportlog\DI\Test\TestCase\Models\Foo;

final class ContainerTest extends TestCase {

    public function testGetValidClass(): void {
        $di = new Container();
        $instance = $di->get(DummyUser::class);
        $this->assertIsObject($instance);
    }

    public function testMappingFactoryGetValidClass(): void {
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

    public function testMappingFactoryWithArgsGetValidClass(): void {
        $di = new Container();

        $di->set(Foo::class, fn (Dummy $dummy): Foo => new Foo($dummy));
        $instance = $di->get(Foo::class);
        $this->assertIsObject($instance);
    }

    public function testMappingFactoryString(): void {
        $di = new Container();

        $di->set('foo', fn ()  => 'some string');
        $this->assertEquals('some string', $di->get('foo'));
    }

    public function testMappingSameInstance(): void {
        $di = new Container();
        
        $di->set(DummyInterface::class, Dummy::class);
        $instance = $di->get(DummyInterface::class);
        $this->assertInstanceOf(DummyInterface::class, $instance);

        $instance2 = $di->get(Dummy::class);
        $this->assertSame($instance2, $instance);
    }

    public function testMappingGetValidClass(): void {
        $di = new Container();
        $di->set(DummyInterface::class, Dummy::class);
        $foo = $di->get(Foo::class);
        $this->assertIsObject($foo);
    }

    public function testGetValidFromFactory(): void {
        $di = new Container();

        $di->set(DummyInterface::class, Dummy::class);
        $di->set(Foo::class, fn (DummyInterface $dummy) => new Foo($dummy), [DummyInterface::class]);

        $foo = $di->get(Foo::class);
        $this->assertIsObject($foo);
        $this->assertEquals('foo', $foo->getFoo());
    }

    public function testHasValidClass(): void {
        $di = new Container();
        $has = $di->has(Foo::class);
        $this->assertTrue($has);
    }

    public function testHasInValidClass(): void {
        $di = new Container();
        $has = $di->has('SomeClass');
        $this->assertFalse($has);
    }

    public function testHasTypeMappedClass(): void {
        $di = new Container();

        $has = $di->has(FooInterface::class);
        $this->assertFalse($has);

        $di->set(FooInterface::class, Foo::class);
        $has = $di->has(FooInterface::class);
        $this->assertTrue($has);
    }
}