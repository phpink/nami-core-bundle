<?php

namespace PhpInk\Nami\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PhpInk\Nami\CoreBundle\DependencyInjection\Compiler\SecurityCompatibilityPass;
use PhpInk\Nami\CoreBundle\DependencyInjection\Compiler\DoctrineManagerCompilerPass;
use PhpInk\Nami\CoreBundle\DependencyInjection\Compiler\PluginScanCompilerPass;

class NamiCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SecurityCompatibilityPass());
        $container->addCompilerPass(new DoctrineManagerCompilerPass());
        //$container->addCompilerPass(new PluginScanCompilerPass());
    }
}
