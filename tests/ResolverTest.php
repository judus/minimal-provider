<?php

namespace Maduser\Minimal\Provider\Tests;

use Maduser\Minimal\Framework\Providers\Contracts\ProviderInterface;
use Maduser\Minimal\Provider\Contracts\AbstractProviderInterface;
use Maduser\Minimal\Provider\Exceptions\ClassDoesNotExistException;
use Maduser\Minimal\Provider\Provider;
use Maduser\Minimal\Provider\Resolver;

use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    /**
     */
    public function testResolveThrowsIocNotResolvable()
    {
        $this->expectException(ClassDoesNotExistException::class);

        $resolver = new Resolver(new Provider());

        $resolver->resolve('dummy');
    }

    public function testResolve()
    {
        $resolver = new Resolver(new Provider(['DummyClass' => DummyClass::class]));

        $result = $resolver->resolve('DummyClass');
        $expected = new DummyClass();

        $this->assertEquals($expected, $result);
    }

    public function testRegisteredIsNull()
    {
        $resolver = new Resolver(new Provider(['DummyClass' => DummyClass::class]));

        $this->assertNull($resolver->registered('Test'));
    }

    public function testRegisteredReturnsProvider()
    {
        $resolver = new Resolver(new Provider(['DummyClass' => DummyProvider::class]));

        $result = $resolver->registered('DummyClass');
        $expected = DummyProvider::class;

        $this->assertEquals($expected, $result);
    }

    public function testRegisteredReturnsSingleton()
    {
        $provider = new Provider();
        $provider->singleton('DummyClass', new DummyProvider());
        $resolver = new Resolver($provider);

        $result = $resolver->registered('DummyClass');
        $expected = new DummyProvider();

        $this->assertEquals($expected, $result);
    }

    public function testHasIsTrue()
    {
        $resolver = new Resolver(new Provider(['DummyClass' => DummyProvider::class]));
        $this->assertEquals(DummyProvider::class, $resolver->has('DummyClass'));
    }

    public function testHasIsFalse()
    {
        $resolver = new Resolver(new Provider(['DummyClass' => DummyProvider::class]));
        $this->assertNull($resolver->has('TestClass'));
    }
}

class DummyClass {}

class DummyProvider implements AbstractProviderInterface
{
    public function init()
    {
        // TODO: Implement init() method.
    }

    public function register()
    {
        // TODO: Implement register() method.
    }

    public function resolve()
    {
        return new DummyClass();
    }

    public function singleton($name, $object)
    {
        // TODO: Implement singleton() method.
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}