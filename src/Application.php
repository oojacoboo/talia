<?php namespace CertifiedWebNinja\Talia;

use Exception;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Response;
use CertifiedWebNinja\Talia\Providers\TaliaServiceProvider;

class Application extends Container
{
    /**
     * Construct application and register Talia Service Provider
     */
    public function __construct($environment = 'production', array $providers = array())
    {
        parent::__construct();
        $this->setEnvironment($environment);
        $this->registerProviders(array_merge($providers, [
            new TaliaServiceProvider
        ]));
    }

    /**
     * Set the environment for the app
     *
     * @param mixed $environment Closure or string
     */
    public function setEnvironment($environment)
    {
        if (is_callable($environment)) $environment = $environment();
        $this['talia.environment'] = $environment;
    }

    /**
     * Return the application environment
     *
     * @return string environment
     */
    public function getEnvironment()
    {
        return $this['talia.environment'];
    }

    /**
     * Route all non-defined methods to routing
     *
     * @param  string  $method  Method to call
     * @param  array   $args    Arguments to pass
     * @return object
     */
    public function __call($method, $args)
    {
        if (!method_exists($this['routes'], $method)) throw new Exception("Method {$method} does not exist");
        return call_user_func_array([$this['routes'], $method], $args);
    }

    /**
     * Bind a service to the container
     *
     * @param  string   $key       Service name
     * @param  closure  $callback  Callable function returning object
     * @param  bool     $override  Override the service if already exists
     * @return object
     */
    public function bind($key, $callback, $override = false)
    {
        if (isset($this[$key]) && !$override) throw new Exception("Unable to bind {$key}");
        $this[$key] = $callback;
        return $this;
    }

    /**
     * Resolve the service from the container
     *
     * @param  string $key Name of service
     * @return object
     */
    public function make($key)
    {
        if (!isset($this[$key])) throw new Exception("{$key} does not exist");
        return $this[$key];
    }

    /**
     * Allow registering multiple providers via an array
     *
     * @param  mixed $providers Provider or array of providers
     * @return void
     */
    public function registerProviders($providers) {
        if(is_array($providers)) {
            foreach ($providers as $provider)
                parent::register($provider);
        } else {
            parent::register($providers);
        }
    }

    /**
     * Dispatch routing and send response
     *
     * @return Response
     */
    public function run()
    {
        $response = $this['dispatcher']->dispatch($this['request']->getMethod(), $this['request']->getPathInfo());
        if ($response instanceof Response) return $response->send();
        else return $this['response']->create($response)->send();
    }
}
