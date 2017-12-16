<?php

namespace Maduser\Minimal\Provider\Tests;

use Maduser\Minimal\Provider\Contracts\AbstractProviderInterface;
use Maduser\Minimal\Provider\Injector;
use Maduser\Minimal\Provider\Provider;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testConstructor()
    {
        $injector = new Injector(new Provider());
    }

    /**
     * @expectedException \Maduser\Minimal\Provider\Exceptions\ClassDoesNotExistException
     */
    public function testReflectThrowsClassDoesNotExist()
    {
        $injector = new Injector(new Provider());
        $injector->reflect('Dummy');
    }

    public function testReflect()
    {
        $injector = new Injector(new Provider());
        $result = $injector->reflect(DummyClassB::class);

        $this->assertTrue($result instanceof \ReflectionClass);
    }

    public function testGetDependency()
    {
        $expected = DummyClassD::class;

        $injector = new Injector(new Provider());
        $reflected = $injector->reflect(DummyClassC::class);
        $parameters = $reflected->getConstructor()->getParameters();
        $result = $injector->getDependency($parameters[0], $reflected);

        $this->assertEquals($expected, $result);
    }

    public function testGetDependencyWithBinding()
    {
        $expected = DummyClassA::class;

        $injector = new Injector(new Provider([], [DummyInterface::class => DummyClassA::class]));
        $reflected = $injector->reflect(DummyClassE::class);
        $parameters = $reflected->getConstructor()->getParameters();
        $result = $injector->getDependency($parameters[0], $reflected);

        $this->assertEquals($expected, $result);
    }

    public function testGetDependencies()
    {
        $expected = [];

        $injector = new Injector(new Provider());
        $reflected = $injector->reflect(DummyClassB::class);
        $result = $injector->getDependencies($reflected);

        $this->assertEquals($expected, $result);

        $expected = [DummyClassB::class, DummyClassC::class];

        $injector = new Injector(new Provider());
        $reflected = $injector->reflect(DummyClassA::class);
        $result = $injector->getDependencies($reflected);

        $this->assertEquals($expected, $result);
    }

    public function testResolveDependencies()
    {
        $expected = [new DummyClassB(), new DummyClassC(new DummyClassD())];

        $injector = new Injector(new Provider());
        $dependencies = [DummyClassB::class, DummyClassC::class];
        $result = $injector->resolveDependencies($dependencies);

        $this->assertEquals($expected, $result);
    }

    public function testResolveDependenciesWithProvider()
    {
        $expected = [new DummyClassB(), new DummyClassD()];

        $injector = new Injector(new Provider([DummyClassC::class => DummyProviderA::class]));
        $dependencies = [DummyClassB::class, DummyClassC::class];
        $result = $injector->resolveDependencies($dependencies);

        $this->assertEquals($expected, $result);
    }

    public function testSetArgsValues()
    {
        $expected = ['a', '123', 'b', '456', 'c'];

        $injector = new Injector(new Provider());
        $result = $injector->setArgsValues([123, 456],
            ['a', null, 'b', null, 'c']);

        $this->assertEquals($expected, $result);
    }

    public function testMakeFromClass()
    {
        $expected = new DummyClassA(
            new DummyClassB(),
            new DummyClassC(
                new DummyClassD()
            )
        );

        $injector = new Injector(new Provider());
        $result = $injector->make(DummyClassA::class);

        $this->assertEquals($expected, $result);
    }

    public function testMakeWithBinding()
    {
        $expected = new DummyClassE(
            new DummyClassF(
                new DummyClassB(),
                new DummyClassC(
                    new DummyClassD()
                )
            )
        );

        $injector = new Injector(new Provider([],
            [DummyInterface::class => DummyClassF::class]
        ));

        $result = $injector->make(DummyClassE::class);

        $this->assertEquals($expected, $result);
    }
}

class DummyClassA
{
    public function __construct(DummyClassB $b, DummyClassC $c) {}
}

class DummyClassB {}

class DummyClassC
{
    public function __construct(DummyClassD $d)
    {
    }
}

class DummyClassD {}

class DummyProviderA implements AbstractProviderInterface
{
    public function init() {}

    public function register() {}

    public function resolve()
    {
        return new DummyClassD();
    }

    public function singleton($name, $object) {}
}

interface DummyInterface {}

class DummyClassE
{
    public function __construct(DummyInterface $dummy) {}
}

class DummyClassF implements DummyInterface
{
    public function __construct(DummyClassB $b, DummyClassC $c)
    {
    }
}
