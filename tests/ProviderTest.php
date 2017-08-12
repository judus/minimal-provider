<?php

namespace Maduser\Minimal\Provider\Tests;

use Maduser\Minimal\Provider\Container;
use Maduser\Minimal\Provider\Injector;
use Maduser\Minimal\Provider\Provider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    public function testConstructor()
    {
        $provider = new Provider();
    }

    public function testConstructorWithParams()
    {
        $provider = new Provider([
            'TestAliasA' => 'TestProviderA',
            'TestAliasB' => 'TestProviderB'
        ], [
            'TestInterfaceA' => 'DummyClassA',
            'TestInterfaceB' => 'DummyClassB'
        ]);

        $this->assertEquals('TestProviderA', $provider->getProvider('TestAliasA'));
        $this->assertEquals('TestProviderB', $provider->getProvider('TestAliasB'));
        $this->assertEquals('DummyClassA', $provider->getBinding('TestInterfaceA'));
        $this->assertEquals('DummyClassB', $provider->getBinding('TestInterfaceB'));
    }

    public function getInjector()
    {
        $expected = new Injector(new Provider());
        $provider = new Provider();
        $result = $provider->getInjector();

        $this->assertEquals($expected, $result);
    }

    public function testSingleton()
    {
        $expected = new DummyObjA();

        $provider = new Provider();
        $provider->singleton(DummyObjA::class, new DummyObjA());

        $result = $provider->singleton(DummyObjA::class);

        $this->assertEquals($expected, $result);
    }

    public function testHasSingleton()
    {
        $expected = new DummyObjA();

        $provider = new Provider();
        $provider->singleton(DummyObjA::class, new DummyObjA());

        $result = $provider->hasSingleton(DummyObjA::class);

        $this->assertEquals($expected, $result);
    }

    public function testHasSingletonReturnsNull()
    {
        $provider = new Provider();
        $result = $provider->hasSingleton('dummy');

        $this->assertNull($result);
    }

    public function testHasProvider()
    {
        $expected = 'dummyClass';

        $provider = new Provider(['dummy' => 'dummyClass']);
        $result = $provider->hasProvider('dummy');

        $this->assertEquals($expected, $result);
    }

    public function testHasProviderReturnsSingleton()
    {
        $expected = new DummyObjA();

        $provider = new Provider(['dummy' => 'dummyObjA']);
        $provider->singleton('dummy', new DummyObjA());
        $result = $provider->hasProvider('dummy');

        $this->assertEquals($expected, $result);
    }


    public function testHasProviderReturnsNull()
    {
        $provider = new Provider();
        $result = $provider->hasProvider('dummy');

        $this->assertNull($result);
    }

    public function testSetAndGetProviders()
    {
        $expected = new Container([
            'dummyA' => 'DummyA',
            'dummyB' => 'DummyB'
        ]);

        $provider = new Provider();
        $provider->setProviders([
            'dummyA' => 'DummyA',
            'dummyB' => 'DummyB'
        ]);

        $result = $provider->providers();

        $this->assertEquals($expected, $result);
    }

    public function testAddProviders()
    {
        $expected = new Container([
            'dummyA' => 'DummyA',
            'dummyB' => 'DummyB'
        ]);

        $provider = new Provider();
        $provider->setProviders([
            'dummyA' => 'DummyA'
        ]);

        $provider->addProviders([
            'dummyB' => 'DummyB'
        ]);

        $result = $provider->providers();

        $this->assertEquals($expected, $result);
    }

    public function testSetAndGetBindings()
    {
        $expected = new Container([
            'dummyA' => 'DummyA',
            'dummyB' => 'DummyB'
        ]);

        $provider = new Provider();
        $provider->setBindings([
            'dummyA' => 'DummyA',
            'dummyB' => 'DummyB'
        ]);

        $result = $provider->bindings();

        $this->assertEquals($expected, $result);
    }

    public function testAddBindings()
    {
        $expected = new Container([
            'dummyA' => 'DummyA',
            'dummyB' => 'DummyB'
        ]);

        $provider = new Provider();
        $provider->setBindings([
            'dummyA' => 'DummyA'
        ]);

        $provider->addBindings([
            'dummyB' => 'DummyB'
        ]);

        $result = $provider->bindings();

        $this->assertEquals($expected, $result);
    }

    public function testBindings()
    {
        $this->testSetAndGetBindings();
    }

    public function testProviders()
    {
        $this->testSetAndGetProviders();
    }
}

class DummyObjA
{

}