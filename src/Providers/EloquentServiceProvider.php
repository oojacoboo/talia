<?php namespace CertifiedWebNinja\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EloquentServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['db'] = function()
        {
            return 'Eloquent';
        };
    }
}