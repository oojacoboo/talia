<?php namespace CertifiedWebNinja\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EloquentServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $this->app = $app;
        $this->registerEloquent();
    }

    private function registerEloquent()
    {
        $this->app['db'] = function()
        {
            return null;
        };
    }
}