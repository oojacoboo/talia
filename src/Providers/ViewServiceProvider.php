<?php namespace CertifiedWebNinja\Talia\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $this->app = $app;
        $this->registerViewParser();
        $this->registerViewLoader();
        $this->registerView();
    }

    private function registerView()
    {
        $this->app['view'] = function()
        {
            return new PhpEngine($this->app['view.parser'], $this->app['view.loader']);
        };
    }

    private function registerViewParser()
    {
        $this->app['view.parser'] = function()
        {
            return new TemplateNameParser;
        };
    }

    private function registerViewLoader()
    {
        $this->app['view.loader'] = function()
        {
            $viewPath = [__DIR__.'/../views'];
            $paths = (isset($this->app['view.paths']) ? array_merge($this->app['view.paths'], $viewPath) : $viewPath);
            $paths = array_map(function($path) {
                return $path.'/%name%';
            }, $paths);
            return new FilesystemLoader($paths);
        };
    }
}