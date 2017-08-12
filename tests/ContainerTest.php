<?php

namespace Maduser\Minimal\Provider\Tests;

use Maduser\Minimal\Provider\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testConstructor()
    {
        $container = new Container();
        $result = $container->get();
        $expected = [];

        $this->assertEquals($expected, $result);
    }

    public function testConstructorWithParams()
    {
        $container = new Container(['dummy', 'test']);
        $result = $container->get();
        $expected = ['dummy', 'test'];

        $this->assertEquals($expected, $result);
    }

    public function testOffsetExistsIsTrue()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container->offsetExists('key2');

        $this->assertTrue($result);
    }

    public function testOffsetExistsIsFalse()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container->offsetExists('key3');

        $this->assertFalse($result);
    }

    public function testOffsetGet()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container['key2'];

        $this->assertEquals('value2', $result);
    }

    public function testOffsetSet()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $container['key2'] = 'abc';

        $result = $container['key2'];

        $this->assertEquals('abc', $result);
    }

    public function testOffsetUnset()
    {
        $container = new Container();
        $container['key2'] = 'abc';
        unset($container['key2']);

        $result = array_key_exists('key2', $container->get());
        $this->assertFalse($result);
    }

    public function test__setAnd__get()
    {
        $container = new Container();
        $container->key2 = 'test';

        $result = $container['key2'];

        $this->assertEquals('test', $result);
    }

    public function testSetAndGet()
    {
        $container = new Container();
        $container->set(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container->get();
        $expected = ['key1' => 'value1', 'key2' => 'value2'];

        $this->assertEquals($expected, $result);
    }

    public function testGetWithOffset()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container->get('key2');

        $this->assertEquals('value2', $result);
    }

    public function testAdd()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $container->add('key3', 'value3');
        $container->add('key4', 'value4');

        $result = $container->get();
        $expected = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4'
        ];

        $this->assertEquals($expected, $result);
    }

    public function testHasIsTrue()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container->has('key2');

        $this->assertTrue($result);
    }

    public function testHasIsFalse()
    {
        $container = new Container(['key1' => 'value1', 'key2' => 'value2']);
        $result = $container->has('key3');

        $this->assertFalse($result);
    }
}
