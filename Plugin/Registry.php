<?php

namespace PhpInk\Nami\CoreBundle\Plugin;

use PhpInk\Nami\CoreBundle\Util\Globals;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class Registry
{
    const PLUGIN_NAMESPACE = 'NamiPlugin';
    const CONFIG_FILENAME = 'config.yml';
    const ROUTES_FILENAME = 'routing.yml';
    const BLOCK_PLUGIN_FILENAME = 'Block';

    /**
     * Self instance
     * @var mixed
     */
    private static $registry = false;

    /**
     * Plugins registered
     * @var array
     */
    private $plugins;

    /**
     * Path to the plugin directory
     * @var string
     */
    private $pluginPath;

    /**
     * Marker to check if each plugin
     * directory has been scanned
     * @var bool
     */
    private $pluginsRegistered = false;

    /**
     * Singleton private constructor
     * @param string $pluginPath
     */
    private function __construct($pluginPath = null)
    {
        if (!is_string($pluginPath)) {
            $pluginPath = Globals::getPluginPath();
        }
        $this->pluginPath = $pluginPath;
    }

    public static function getInstance($pluginPath = null)
    {
        if (self::$registry === false) {
            self::$registry = new self($pluginPath);
        }
        return self::$registry;
    }
    
    /**
     * Get the value of plugins
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Set the value of plugins
     * @param array $plugins
     */
    public function setPlugins($plugins)
    {
        $this->plugins = $plugins;
    }

    public function getPlugin($key)
    {
        $plugin = false;
        if (array_key_exists($key, $this->plugins)) {
            $plugin = $this->plugins[$key];
        }
        return $plugin;
    }

    public function scanPlugins()
    {
        $directory = new \DirectoryIterator($this->pluginPath);
        foreach ($directory as $dirChild) {
            if ($dirChild->isDir() && !$dirChild->isDot()) {
                $name = $dirChild->getFilename();
                $this->plugins[$name] = array(
                    'namespace' => '\\'. self::PLUGIN_NAMESPACE. '\\'. $name,
                    'path' => $dirChild->getRealPath()
                );
                $blockPluginFilename = $this->plugins[$name]['path']. '/'.
                    self::BLOCK_PLUGIN_FILENAME. '.php';
                if (file_exists($blockPluginFilename)) {
                    $this->plugins[$name]['block'] = $this->plugins[$name]['namespace'].
                        '\\'. self::BLOCK_PLUGIN_FILENAME;
                }
            }
        }
        $this->scanPluginsExtraFiles();
        return $this->plugins;
    }

    public function scanPluginsExtraFiles()
    {
        if (!$this->pluginsRegistered) {
            foreach ($this->plugins as $key => $plugin) {
                $pluginFiles = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($plugin['path']),
                    \RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($pluginFiles as $pluginFile) {
                    $filename = $pluginFile->getFilename();
                    // Search for config files
                    if ($filename === self::CONFIG_FILENAME) {
                        $this->plugins[$key][self::CONFIG_FILENAME] =
                            $pluginFile->getPath();
                    // Search for routing files
                    } else if ($filename === self::ROUTES_FILENAME) {
                        $this->plugins[$key][self::ROUTES_FILENAME] =
                            $pluginFile->getPath();
                    }
                }
            }
            $this->pluginsRegistered = true;
        }
        return $this->plugins;
    }

    public function registerConfig(ContainerBuilder $container)
    {
        foreach ($this->plugins as $plugin) {
            if (array_key_exists(self::CONFIG_FILENAME, $plugin)) {

                $loader = new YamlFileLoader(
                    $container,
                    new FileLocator($plugin[self::CONFIG_FILENAME])
                );
                $loader->load(self::CONFIG_FILENAME);
            }
        }
    }

    public function getRoutedPlugins()
    {
        $routedPlugins = array();
        foreach ($this->plugins as $name => $plugin) {
            if (array_key_exists(self::ROUTES_FILENAME, $plugin)) {
                $routedPlugins[$name] = $plugin[self::ROUTES_FILENAME].
                    '/'. self::ROUTES_FILENAME;
            }
        }
        return $routedPlugins;
    }
}
