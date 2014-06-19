<?php namespace Killswitch\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TaliaServiceProvider implements ServiceProviderInterface
{
    /**
     * Some information about Talia
     */
    const NAME = 'Talia PHP Micro-Framework';
    const VERSION = '2.0.0';
    const AUTHOR = 'Josh Manders';
    const AUTHOR_EMAIL = 'josh@joshmanders.com';
    const AUTHOR_URL = 'http://www.joshmanders.com';

    /**
     * Instance of Application Container
     *
     * @var object
     */
    private $app;

    /**
     * Default providers to load
     *
     * @var array
     */
    private $providers = [
        'WhoopsServiceProvider',
        'RoutingServiceProvider'
    ];

    /**
     * Register the service provider
     *
     * @param  Container $app Instance of container
     * @return void
     */
    public function register(Container $app)
    {
        $this->app = $app;
        $this->setupAboutDetails();
        $this->registerDefaultProviders();
    }

    /**
     * Setup the about details for Talia
     *
     * @return void
     */
    private function setupAboutDetails()
    {
        $this->app['app.name'] = self::NAME;
        $this->app['app.version'] = self::VERSION;
        $this->app['app.author'] = self::AUTHOR;
        $this->app['app.author.email'] = self::AUTHOR_EMAIL;
        $this->app['app.author.url'] = self::AUTHOR_URL;
    }

    /**
     * Register all default providers
     *
     * @return void
     */
    private function registerDefaultProviders()
    {
        foreach ($this->providers as $provider)
        {
            $class = __NAMESPACE__.'\\'.$provider;
            $this->app->register(new $class);
        }
    }
}