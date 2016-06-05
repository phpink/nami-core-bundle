<?php namespace PhpInk\Nami\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * The plugin.register event is dispatched to get the list of the plugins
 */
class PluginRegisterEvent extends Event
{
    const NAME = 'nami.plugin.register';

    /**
     * @var array
     */
    public $registeredPlugins;

    public function __construct(&$registeredPlugins)
    {
        $this->registeredPlugins = &$registeredPlugins;
    }

    /**
     * @param string $pluginName
     * @return array
     */
    public function registerPlugin($pluginName)
    {
        return $this->registeredPlugins[] = $pluginName;
    }
}
