<?php namespace Killswitch\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class WhoopsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['whoops.error_page_handler'] = function()
        {
            return new PrettyPageHandler;
        };
        $app['whoops'] = function() use($app)
        {
            $run = new Run;
            $run->allowQuit(false);
            $run->pushHandler($app['whoops.error_page_handler']);
            return $run;
        };
        $app['whoops']->register();
    }
}