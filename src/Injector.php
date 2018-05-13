<?php namespace Maduser\Minimal\Provider;

use App\Modules\ContactForm\Admin\Controllers\ContactFormController;
use Maduser\Minimal\Middlewares\Middleware;
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
        if ($class == 'array') {
            return [];
        }

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
    public function getDependency(\ReflectionParameter $parameter, \ReflectionClass $reflected)
    {
        if ($parameter->isArray() || !$parameter->getClass()) {

            if ($parameter->isArray()) {
                return 'array';
            }

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
                $dependencies[] = $this->getDependency($parameter, $reflected);
            }
        }

        return $dependencies;
    }

    /**
     * @param array $dependencies
     *
     * @return array
     */
    public function resolveDependencies(array $dependencies, $params = null)
    {
        foreach ($dependencies as &$dependency) {
            if (is_null($dependency)) {
                $dependency = null;
            } else if ($dependency == 'array') {
                $dependency = [];
            } else {

                if ($this->provider->hasProvider($dependency)) {
                    $dependency = $this->provider->resolve($dependency, $params);
                } else {
                    $dependency = $this->make($dependency, $params);
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

        if ($params) {
            foreach ($args as &$arg) {
                if (is_array($arg)) {
                    foreach ($params as &$param) {
                        if (is_array($param)) {
                            $arg = $param;
                            unset($param);
                            break;
                        }
                    }
                }
            }
        }

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
     * @throws ClassDoesNotExistException
     */
    public function make($class, array $params = null)
    {
        if ($class == 'array') {
            return [];
        }

        $reflected = $this->reflect($class);

        if (empty($reflected->getConstructor())) {

            if ($reflected->isInterface()) {

                if ($match = $this->findMatchInParams($reflected, $params)) {
                    return $this->make($match->getName());
                }

                throw new IocNotResolvableException(
                    'Cannot instantiate ' . $reflected->getName()
                );
            }

            return $reflected->newInstance();
        }

        $dependencies = $this->getDependencies($reflected);

        $instanceArgs = $this->resolveDependencies($dependencies, $params);

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

    public function findMatchInParams($reflected, array $params = null)
    {
        if (! $params) return null;

        foreach ($params as $param) {
            if (is_object($param)) {
                $paramReflection = new \ReflectionClass($param);
                if ($paramReflection->implementsInterface($reflected->getName())) {
                    return $paramReflection;
                }
            }
        }

        return null;
    }

}