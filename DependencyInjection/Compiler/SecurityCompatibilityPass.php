<?php

namespace PhpInk\Nami\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use PhpInk\Nami\CoreBundle\Util\ContainerBuilderAwareTrait;

/**
 * Injects the security context or token storage/authorization checker,
 * depending on the Symfony version.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SecurityCompatibilityPass implements CompilerPassInterface
{
    use ContainerBuilderAwareTrait;

    public function process(ContainerBuilder $container)
    {
        $this->setContainer($container);
        $this->setTokenManagerReference();
        $this->setEncoderReference();
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
}