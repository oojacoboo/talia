<?php namespace CertifiedWebNinja\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $this->app = $app;
        $this->registerSession();
    }

    /**
     * Register a session handler
     *
     * @return void
     */
    private function registerSession()
    {
        $this->app['session'] = function()
        {
            $session = new Session();
            $session->start();
            return $session;
        };
    }
}