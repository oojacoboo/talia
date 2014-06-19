<?php namespace Killswitch\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Phroute\RouteCollector;
use Phroute\RouteParser;
use Phroute\Dispatcher;

class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['routes'] = function($app)
        {
            return new RouteCollector(new RouteParser);
        };
        $app['dispatcher'] = function($app)
        {
            return new Dispatcher($app['routes']);
        };
        $app['request.method'] = $_SERVER['REQUEST_METHOD'];
        $app['request.uri'] = $_SERVER['REQUEST_URI'];
    }
}