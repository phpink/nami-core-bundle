<?php

namespace PhpInk\Nami\CoreBundle\Plugin;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    /**
     * Route is loaded
     * @var boolean
     */
    private $loaded = false;

    private $pluginPath;

    public function __construct($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'namiplugin';
    }

    public function load($resource, $type = null)
    {
        if ($this->loaded) {
            throw new \RuntimeException(
                'The plugin routing loader must not be run twice'
            );
        }

        // Add the plugin routes
        $routes   = new RouteCollection();
        $plugins = Registry::getInstance($this->pluginPath)->getRoutedPlugins();
        foreach ($plugins as $plugin => $routerFile) {
            $routes->addCollection(
                $this->import($routerFile)
            );
        }
        $this->loaded = true;
        return $routes;
    }
}
