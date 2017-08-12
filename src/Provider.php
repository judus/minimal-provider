<?php namespace Maduser\Minimal\Provider;

use Maduser\Minimal\Provider\Contracts\ProviderInterface;
use Maduser\Minimal\Provider\Exceptions\IocNotResolvableException;

/**
 * Class Provider
 *
 * @package Maduser\Minimal\Provider
 */
class Provider implements ProviderInterface
{
    /**
     * @var
     */
    private $providers;

    /**
     * @var
     */
    private $bindings;

    /**
     * @var Container
     */
    private $singletons;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Injector
     */
    private $injector;

    /**
     * @return Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }

    /**
     * Provider constructor.
     *
     * @param array $providers
     * @param array $bindings
     */
    public function __construct(
        array $providers = null,
        array $bindings = null
    ) {
        $this->injector = new Injector($this);
        $this->resolver = new Resolver($this);

        $this->setBindings(is_null($bindings) ? [] : $bindings);
        $this->setProviders(is_null($providers) ? [] : $providers);

        $this->singletons = new Container();

        $this->singleton(get_class($this), $this);
    }

    /**
     * @param $name
     *
     * @return array|mixed|null
     */
    public function hasSingleton($name)
    {
        if ($this->singletons->has($name)) {
            return $this->singletons->get($name);
        }

        return null;
    }

    /**
     * @param $name
     *
     * @return array|mixed|null
     */
    public function hasProvider($name)
    {

        if ($this->singletons->has($name)) {
            return $this->singletons->get($name);
        }

        if ($this->providers->has($name)) {
            return $this->providers->get($name);
        }

        $alias = basename(str_replace('\\', '/', $name));

        if ($this->singletons->has($alias)) {
            return $this->singletons->get($alias);
        }

        if ($this->providers->has($alias)) {
            return $this->providers->get($alias);
        }

        return null;
    }

    /**
     * @param      $name
     * @param null $object
     *
     * @return mixed|null
     */
    public function singleton($name, $object = null)
    {
        if ($object) {
            return $this->singletons[$name] = $object;
        } else {
            return $this->singletons[$name];
        }
    }

    /**
     * @param $providers
     */
    public function setProviders($providers)
    {
        $this->providers = new Container($providers);
    }

    /**
     * @param $providers_
     */
    public function addProviders($providers_)
    {
        $providers = $this->providers->get();
        $this->providers = new Container(array_merge($providers, $providers_));
    }

    /**
     * @param $bindings
     */
    public function setBindings($bindings)
    {
        $this->bindings = new Container($bindings);
    }

    /**
     * @param $bindings_
     */
    public function addBindings($bindings_)
    {
        $bindings = $this->bindings->get();
        $this->bindings = new Container(array_merge($bindings, $bindings_));
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getProvider($name)
    {
        return $this->providers->get($name);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function hasBinding($name)
    {
        return $this->bindings->has($name);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getBinding($name)
    {
        return $this->bindings->get($name);
    }

    /**
     * @param      $name
     * @param null $params
     *
     * @return mixed
     * @throws IocNotResolvableException
     */
    public function resolve($name, $params = null)
    {
        if (! $resolved = $this->resolver->resolve($name, $params)) {

        }

        return $resolved;
    }

    /**
     * @param      $name
     * @param null $params
     *
     * @return object
     */
    public function make($name, $params = null)
    {
        return $this->injector->make($name, $params);
    }

    /**
     * @param $name
     * @param $class
     */
    public function register($name, $class)
    {
        $this->providers->add($name, $class);
    }

    /**
     * @param $name
     * @param $class
     */
    public function bind($name, $class)
    {
        $this->bindings->add($name, $class);
    }

    /**
     * @return mixed
     */
    public function bindings()
    {
        return $this->bindings;
    }

    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->providers;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function registered($name)
    {
        return $this->providers->has($name);
    }
}