<?php

namespace PhpInk\Nami\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PhpInk\Nami\CoreBundle\Plugin\Registry as PluginRegistry;

/**
 * PluginScanCompilerPass :
 * Scans plugin directory
 */
class PluginScanCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $pluginRegistry = PluginRegistry::getInstance(
            str_replace(
                '%kernel.root_dir%',
                $container->getParameter('kernel.root_dir'),
                $container->getParameter('nami_core.plugin_path')
            )
        );
        $pluginRegistry->scanPlugins();
        $pluginRegistry->registerConfig($container);
    }
}
