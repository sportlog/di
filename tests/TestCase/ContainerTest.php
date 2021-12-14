<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase;

use PHPUnit\Framework\TestCase;
use Sportlog\DI\Container;

final class ContainerTest extends TestCase {
    public function testGetValidClass(): void {
        $di = new Container();
        
        $b = new Dummy();
        $di->set(DummyInterface::class, fn() => $b);
        $this->assertTrue($di->has(DummyInterface::class));
        $this->assertSame($b, $di->get(DummyInterface::class));

        $this->assertIsObject($di->get(DummyUser::class));
    }

    public function testGetValidClassWithArgs(): void {
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
}