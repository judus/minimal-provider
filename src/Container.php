<?php namespace Maduser\Minimal\Provider;

/**
 * Class Container
 *
 * @package Maduser\Minimal\Provider
 */
class Container implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $array = [];

    /**
     * Container constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->array = $items;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->array);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->array[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function __set($offset, $value)
    {
        $this[$offset] = $value;
    }

    /**
     * @param $offset
     *
     * @return mixed
     */
    public function __get($offset)
    {
        return $this[$offset];
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function set($value)
    {
        $this->array = $value;

        return $this;
    }

    /**
     * @param null $offset
     *
     * @return array|mixed
     */
    public function get($offset = null)
    {
        if ($offset) {
            return $this[$offset];
        }

        return $this->array;
    }

    /**
     * @param $offset
     * @param $value
     *
     * @return $this
     */
    public function add($offset, $value)
    {
        $this->array[$offset] = $value;

        return $this;
    }

    /**
     * @param $offset
     *
     * @return bool
     */
    public function has($offset)
    {
        return $this->offsetExists($offset);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
      return $this->array;
    }
}