<?php

namespace PhpInk\Nami\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use PhpInk\Nami\CoreBundle\Util\ContainerBuilderAwareTrait;

/**
 * DoctrineManagerCompilerPass :
 * Distributes Doctrine manager service instance
 * to various bundle services, depending on config (ORM/ODM)
 */
class DoctrineManagerCompilerPass implements CompilerPassInterface
{
    use ContainerBuilderAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->setContainer($container);
        $this->setDoctrineManagerReference(
            $container->getParameter('nami_core.database_adapter')
        );
    }

    /**
     * Sets the Doctrine manager service reference
     *
     * @param $dbAdapter string
     * @return Reference
     */
    private function setDoctrineManagerReference($dbAdapter)
    {
        $services = array(
            'nami_core.controller_listener', // CORE //
            'nami_core.user_provider',

            'nami_api.json_decoder',         // API  //
            'nami_api.authentication_success_listener',
        );

        foreach ($services as $service) {
            if ($this->container->hasDefinition($service)) {
                $this->container
                    ->findDefinition($service)
                    ->addMethodCall('setManager', [
                        $this->getManager($dbAdapter)
                    ]);
            }
        }
    }

    /**
     * Return the manager service reference for the specified db adapter
     *
     * @param $dbAdapter string
     * @return Reference
     */
    private function getManager($dbAdapter)
    {
        return new Reference(
            $dbAdapter === 'odm' ?
                'doctrine_mongodb.odm.document_manager' :
                'doctrine.orm.entity_manager'
        );
    }
}
