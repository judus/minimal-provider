<?php namespace Maduser\Minimal\Provider;

use Maduser\Minimal\Provider\Contracts\AbstractProviderInterface;
use Maduser\Minimal\Provider\Exceptions\IocNotResolvableException;
use Maduser\Minimal\Provider\Provider;

/**
 * Class Resolver
 *
 * @package AMaduser\Minimal\Provider
 */
class Resolver
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * Resolver constructor.
     *
     * @param Provider $provider
     */
    public function __construct(
        Provider $provider
    ) {
        $this->provider = $provider;
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
        if ($registered = $this->provider->hasProvider($name)) {

            if (!is_object($registered) && !is_callable($registered)) {
                $registered = $this->provider->getInjector()
                                             ->make($registered, $params);
            }

            if ($this->isProvider($registered)) {
                return $registered->resolve($params);
            }

            return $registered;
        }

        throw new IocNotResolvableException($name);
    }

    /**
     * @param $instance
     *
     * @return bool
     */
    public function isProvider($instance)
    {
        $interfaces = class_implements($instance);

        if (isset($interfaces[AbstractProviderInterface::class])) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function registered($name)
    {
        if ($this->provider->hasSingleton($name)) {
            return $this->provider->singleton($name);
        }

        if ($this->provider->hasProvider($name)) {
            return $this->provider->getProvider($name);
        }

        return null;
    }

    /**
     * @param $name
     *
     * @return array|mixed|null
     */
    public function has($name)
    {
        return $this->provider->hasProvider($name);
    }

}