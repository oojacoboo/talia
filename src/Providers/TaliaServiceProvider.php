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
        $this->registerInput();
        $this->registerServer();
        $this->registerFiles();
        $this->registerHeaders();
        $this->registerResponse();
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

    private function registerInput()
    {
        $this->app['input'] = function()
        {
            return $this->app['request']->query;
        };
    }

    private function registerServer()
    {
        $this->app['request.server'] = function()
        {
            return $this->app['request']->server;
        };
    }

    private function registerFiles()
    {
        $this->app['input.files'] = function()
        {
            return $this->app['request']->files;
        };
    }

    private function registerHeaders()
    {
        $this->app['request.headers'] = function()
        {
            return $this->app['request']->headers;
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
            $pretty = new PrettyPageHandler;
            $pretty->setPageTitle('ERROR!!!');
            $pretty->addDataTable($this->app['talia.name'], [
                'Version' => $this->app['talia.version']
            ]);
            return $pretty;
        };
        $this->app['whoops.json_response'] = function()
        {
            $json = new JsonResponseHandler;
            $json->addTraceToOutput(true);
            $json->onlyForAjaxRequests(true);
            return $json;
        };
        $this->app['whoops'] = function()
        {
            $whoops = new Run;
            $whoops->allowQuit(true);
            $whoops->pushHandler($this->app['whoops.json_response']);
            $whoops->pushHandler($this->app['whoops.pretty_page']);
            return $whoops;
        };
        $this->app['whoops.pretty_page']->setPageTitle('Something broke!');
        if ($this->app['talia.environment'] != 'production') $this->app['whoops']->register();
    }
}