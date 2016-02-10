<?php

namespace PhpInk\Nami\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use PhpInk\Nami\CoreBundle\Util\ContainerAwareTrait;

/**
 * DoctrineManagerCompilerPass :
 * Distributes Doctrine manager service instance
 * to various bundle services, depending on config (ORM/ODM)
 */
class ServiceDefinitionCompilerPass implements CompilerPassInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->setContainer($container);
        $this->setDoctrineManagerReference(
            $container->getParameter('nami_core.database_adapter')
        );
        $this->setTokenManagerReference();
        $this->setEncoderReference();
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
     * Sets the Token manager service reference
     *
     * @return Reference
     */
    private function setTokenManagerReference()
    {
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $tokenStorageReference = new Reference('security.token_storage');
        } else {
            $tokenStorageReference = new Reference('security.context');
        }
        $this->container
            ->getDefinition('nami_core.references_preserialize_subscriber')
            ->replaceArgument(0, $tokenStorageReference);
    }

    /**
     * Sets the Password Encoder service reference
     *
     * @return Reference
     */
    private function setEncoderReference()
    {
        if (interface_exists('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')) {
            $encoderReference = new Reference('security.password_encoder');
        } else {
            $encoderReference = new Reference('security.encoder_factory');
        }
        $this->container
            ->getDefinition('nami_core.user_provider')
            ->replaceArgument(0, $encoderReference);
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
