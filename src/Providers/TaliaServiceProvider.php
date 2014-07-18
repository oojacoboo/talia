<?php namespace CertifiedWebNinja\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Phroute\RouteCollector;
use Phroute\RouteParser;
use Phroute\Dispatcher;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class TaliaServiceProvider implements ServiceProviderInterface
{
    /**
     * Some information about Talia
     */
    const NAME = 'Talia PHP Micro-Framework';
    const VERSION = '2.0.1';
    const AUTHOR = 'Josh Manders';
    const AUTHOR_EMAIL = 'josh@joshmanders.com';
    const AUTHOR_URL = 'http://www.certifiedwebninja.com';

    /**
     * Instance of Application Container
     *
     * @var object
     */
    private $app;

    /**
     * Register the service provider
     *
     * @param  Container $app Instance of container
     * @return void
     */
    public function register(Container $app)
    {
        $this->app = $app;
        $this->registerTaliaAbout();
        $this->registerRequest();
        $this->registerResponse();
        $this->registerSession();
        $this->registerPhroute();
        $this->registerWhoops();
    }

    /**
     * Setup the about details for Talia
     *
     * @return void
     */
    private function registerTaliaAbout()
    {
        $this->app['talia.name'] = self::NAME;
        $this->app['talia.version'] = self::VERSION;
        $this->app['talia.author'] = self::AUTHOR;
        $this->app['talia.author.email'] = self::AUTHOR_EMAIL;
        $this->app['talia.author.url'] = self::AUTHOR_URL;
    }

    /**
     * Register request object
     *
     * @return void
     */
    private function registerRequest()
    {
        $this->app['request'] = function()
        {
            return Request::createFromGlobals();
        };
    }

    /**
     * Register response object
     *
     * @return void
     */
    private function registerResponse()
    {
        $this->app['response'] = function()
        {
            return new Response;
        };
    }

    /**
     * Register sessions
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

    /**
     * Register Phroute routing
     *
     * @return void
     */
    private function registerPhroute()
    {
        $this->app['routes'] = function()
        {
            return new RouteCollector(new RouteParser);
        };
        $this->app['dispatcher'] = function()
        {
            return new Dispatcher($this->app['routes']);
        };
    }

    /**
     * Register Whoops! error handling
     *
     * @return void
     */
    private function registerWhoops()
    {
        $this->app['whoops.pretty_page'] = function()
        {
            return new PrettyPageHandler;
        };
        $this->app['whoops.json_response'] = function()
        {
            return new JsonResponseHandler;
        };
        $this->app['whoops'] = function()
        {
            $run = new Run;
            $run->allowQuit(false);
            $run->pushHandler($this->app['whoops.json_response']);
            $run->pushHandler($this->app['whoops.pretty_page']);
            return $run;
        };
        $this->app['whoops.pretty_page']->setPageTitle('Something broke!');
        if ($this->app['talia.environment'] != 'production') $this->app['whoops']->register();
    }
}