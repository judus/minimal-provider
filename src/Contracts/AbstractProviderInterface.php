<?php namespace Maduser\Minimal\Provider\Contracts;

/**
 * Interface AbstractProviderInterface
 *
 * @package Maduser\Minimal\Provider\Contracts
 */
interface AbstractProviderInterface
{
    /**
     * @return mixed
     */
    public function init();

    /**
     * @return mixed
     */
    public function register();

    /**
     * @return mixed
     */
    public function resolve();

    /**
     * @param $name
     * @param $object
     *
     * @return mixed
     */
    public function singleton($name, $object);
}