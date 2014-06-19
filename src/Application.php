<?php namespace Killswitch\Talia;

use Exception;
use Pimple\Container;
use Killswitch\Talia\Providers\TaliaServiceProvider;

class Application extends Container
{
    /**
     * Construct application and register Talia Service Provider
     */
    public function __construct($debug = false)
    {
        parent::__construct();
        $this->register(new TaliaServiceProvider($debug));
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
     * Run the application and process routing
     *
     * @return mixed Contents of the route
     */
    public function run()
    {
        echo $this['dispatcher']->dispatch($this['request.method'], $this['request.uri']);
    }
}
