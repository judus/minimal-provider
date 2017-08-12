<?php namespace Maduser\Minimal\Provider;

use Maduser\Minimal\Provider\Exceptions\ClassDoesNotExistException;
use Maduser\Minimal\Provider\Exceptions\IocNotResolvableException;
use Maduser\Minimal\Provider\Exceptions\UnresolvedDependenciesException;

/**
 * Class Injector
 *
 * @package Maduser\Minimal\Provider
 */
class Injector
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * Injector constructor.
     *
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param $class
     *
     * @return \ReflectionClass
     * @throws ClassDoesNotExistException
     */
    public function reflect($class)
    {
        try {
            return new \ReflectionClass($class);
        } catch (\Exception $e) {
            throw new ClassDoesNotExistException('Class ' . $class . ' does not exist');
        }
    }

    /**
     * @param \ReflectionParameter $parameter
     *
     * @return mixed|null
     */
    public function getDependency(\ReflectionParameter $parameter)
    {
        if ($parameter->isArray() || !$parameter->getClass()) {
            return null;
        }

        $class = $parameter->getClass()->name;

        if ($class == 'Closure') {
            return null;
        }

        $reflected = new \ReflectionClass($class);

        if ($this->provider->hasBinding($reflected->name)) {
            return $this->provider->getBinding($reflected->name);
        } else {
            // TODO: inspect this
            return $reflected->name;
        }
    }

    /**
     * @param \ReflectionClass $reflected
     *
     * @return array
     */
    public function getDependencies(\ReflectionClass $reflected)
    {
        $dependencies = [];

        if ($constructor = $reflected->getConstructor()) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $dependencies[] = $this->getDependency($parameter);
            }
        }

        return $dependencies;
    }

    /**
     * @param array $dependencies
     *
     * @return array
     */
    public function resolveDependencies(array $dependencies)
    {
        foreach ($dependencies as &$dependency) {
            if (is_null($dependency)) {
                $dependency = null;
            } else {

                if ($this->provider->hasProvider($dependency)) {
                    $dependency = $this->provider->resolve($dependency);
                } else {
                    $dependency = $this->make($dependency);
                }
            }
        }

        return $dependencies;
    }

    /**
     * @param array|null $params
     * @param array      $instanceArgs
     *
     * @return array
     */
    public function setArgsValues(array $params = null, array $instanceArgs)
    {
        if (is_array($params)) {
            foreach ($params as $param) {
                foreach ($instanceArgs as &$instanceArg) {
                    if (is_null($instanceArg)) {
                        $instanceArg = $param;
                        break;
                    }
                }
            }
        }

        return $instanceArgs;
    }

    /**
     * @param array      $args
     * @param array|null $params
     *
     * @return array
     */
    public function mergeArgsAndParams(array $args, array $params = null)
    {
        if (is_array($params)) {
            return array_merge($args, $params);
        }

        return $args;
    }

    /**
     * @param            $class
     * @param array|null $params
     *
     * @return object
     * @throws IocNotResolvableException
     * @throws UnresolvedDependenciesException
     */
    public function make($class, array $params = null)
    {
        $reflected = $this->reflect($class);

        if (empty($reflected->getConstructor())) {

            if ($reflected->isInterface()) {
                throw new IocNotResolvableException(
                    'Cannot instantiate ' . $reflected->getName()
                );
            }

            return $reflected->newInstance();
        }

        $dependencies = $this->getDependencies($reflected);

        $instanceArgs = $this->resolveDependencies($dependencies);

        $instanceArgs = $this->setArgsValues($params, $instanceArgs);

        if (count($dependencies) != count($instanceArgs)) {
            throw new UnresolvedDependenciesException(
                'Could not resolve all dependencies', [
                'Required' => $dependencies,
                'Resolved' => $instanceArgs
            ]);
        }

        $instanceArgs = $this->mergeArgsAndParams($instanceArgs, $params);

        return $reflected->newInstanceArgs($instanceArgs);
    }

}