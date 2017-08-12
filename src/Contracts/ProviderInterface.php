<?php
/**
 * ProviderInterface.php
 * 8/12/17 - 11:01 PM
 *
 * PHP version 7
 *
 * @package    @package_name@
 * @author     Julien Duseyau <julien.duseyau@gmail.com>
 * @copyright  2017 Julien Duseyau
 * @license    https://opensource.org/licenses/MIT
 * @version    Release: @package_version@
 *
 * The MIT License (MIT)
 *
 * Copyright (c) Julien Duseyau <julien.duseyau@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Maduser\Minimal\Provider\Contracts;

use Maduser\Minimal\Provider\Exceptions\IocNotResolvableException;
use Maduser\Minimal\Provider\Injector;


/**
 * Class Provider
 *
 * @package Maduser\Minimal\Provider
 */
interface ProviderInterface
{
    /**
     * @return Injector
     */
    public function getInjector();

    /**
     * @param $name
     *
     * @return array|mixed|null
     */
    public function hasSingleton($name);

    /**
     * @param $name
     *
     * @return array|mixed|null
     */
    public function hasProvider($name);

    /**
     * @param      $name
     * @param null $object
     *
     * @return mixed|null
     */
    public function singleton($name, $object = null);

    /**
     * @param $providers
     */
    public function setProviders($providers);

    /**
     * @param $providers_
     */
    public function addProviders($providers_);

    /**
     * @param $bindings
     */
    public function setBindings($bindings);

    /**
     * @param $bindings_
     */
    public function addBindings($bindings_);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getProvider($name);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function hasBinding($name);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getBinding($name);

    /**
     * @param      $name
     * @param null $params
     *
     * @return mixed
     * @throws IocNotResolvableException
     */
    public function resolve($name, $params = null);

    /**
     * @param      $name
     * @param null $params
     *
     * @return object
     */
    public function make($name, $params = null);

    /**
     * @param $name
     * @param $class
     */
    public function register($name, $class);

    /**
     * @param $name
     * @param $class
     */
    public function bind($name, $class);

    /**
     * @return mixed
     */
    public function bindings();

    /**
     * @return mixed
     */
    public function providers();

    /**
     * @param $name
     *
     * @return mixed
     */
    public function registered($name);
}