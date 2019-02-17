<?php namespace Maduser\Minimal\Provider;

/**
 * Class Container
 *
 * @package Maduser\Minimal\Provider
 */
class Container implements \ArrayAccess, \Iterator
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

    public function each($closure)
    {
        $container = new static();

        foreach ($this->array as $key => $item) {
            $container->add($key, $closure($item));
        }

        return $container;
    }

    /* Iterator */

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->array);
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->array);
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->array);
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->array);
    }

}