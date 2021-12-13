<?php

declare(strict_types = 1);

namespace Sportlog\DI\Test\TestCase;

use PHPUnit\Framework\TestCase;
use Sportlog\DI\Container;
use Sportlog\DI\Exception\NotFoundException;
use Sportlog\DI\Exception\ContainerException;

final class ContainerTest extends TestCase {
    public function testGetValidClass(): void {
        $b = new Dummy();
        
        $di = new Container();
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
    
    // /**
    //  * Undocumented function
    //  *
    //  * @return void
    //  */
    // public function testHasInvalidClassThrows(): void {
    //     $this->expectException(InvalidArgumentException::class);
        
    //     $di = new Container();
    //     $di->has(new Begleiter([]));  // not a valid class type        
    // }

    /**
     * Creating a class with mandatory union-type must throw
     *
     * @return void
     */
    public function testNotNullableParameterThrows(): void {
        $this->expectException(ContainerException::class);
        
        $di = new Container();
        $di->get(Foo::class);
    }
 
    /**
     * Creating an non-registered type must throw
     *
     * @return void
     */
    public function testUnregisteredNotInstantiableTypeThrows(): void {
        $this->expectException(ContainerException::class);
        
        $di = new Container();
        $di->get(DummyInterface::class); // must throw as DummyInterface is not registered
    }
    
    /**
     * Creating an invalid class must throw
     *
     * @return void
     */
    public function testInvalidClassThrows(): void {
        $this->expectException(NotFoundException::class);
        
        $di = new Container();
        $di->get("dummy"); // some invalid class
    }
    
    /**
     * Creating a recursive type must throw
     *
     * @return void
     */
    public function testGettingRecursiveTypeThrows(): void {
        $this->expectException(ContainerException::class);
        
        $di = new Container();
        $di->get(DummyRecursive::class);
    }
}