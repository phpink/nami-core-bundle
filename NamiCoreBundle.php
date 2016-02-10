<?php

namespace PhpInk\Nami\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PhpInk\Nami\CoreBundle\DependencyInjection\Compiler\ServiceDefinitionCompilerPass;

class NamiCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(
            new ServiceDefinitionCompilerPass()
        );
    }
}
