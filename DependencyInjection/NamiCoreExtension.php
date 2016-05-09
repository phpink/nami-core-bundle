<?php

namespace PhpInk\Nami\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use PhpInk\Nami\CoreBundle\Util\ContainerBuilderAwareTrait;

class NamiCoreExtension extends Extension implements PrependExtensionInterface
{
    use ContainerBuilderAwareTrait;

    /**
     * Required bundle dependencies
     * @var array
     */
    private $requiredBundles = array(
        'DoctrineBundle', 'StofDoctrineExtensionsBundle', // CORE //
        'JMSSerializerBundle', 'BazingaHateoasBundle',
        'LiipImagineBundle',

        'FOSRestBundle', 'SensioFrameworkExtraBundle',    // API  //
        'NelmioApiDocBundle', 'LexikJWTAuthenticationBundle', 'GfreeauGetJWTBundle'
    );

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->setContainer($container);
        $this->checkRequiredBundles();
        $this->coreConfiguration();
        $this->apiConfiguration();
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter(
            'jms_serializer.camel_case_naming_strategy.class',
            'JMS\Serializer\Naming\IdenticalPropertyNamingStrategy'
        );
    }

    /**
     * Check dependencies
     */
    private function checkRequiredBundles()
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        foreach ($this->requiredBundles as $requiredBundle) {
            if (!array_key_exists($requiredBundle, $bundles)) {
                throw new \RuntimeException(
                    sprintf('%s must be installed to use NamiCoreBundle.', $requiredBundle)
                );
            }
        }
    }

    /**
     * Configures core services
     */
    private function coreConfiguration()
    {
        /**
         * Doctrine mapping &
         * DoctrineExtensions listeners
         */
        $dbAdapter = $this->container->getParameter('nami_core.database_adapter');
        $mappingsInfo = array(

            'tree' => array(
                'type' => 'annotation',
                'prefix' => $dbAdapter === 'orm' ? 'Gedmo\Tree\Entity' : 'Gedmo\Tree\Document',
                'dir' => $dbAdapter === 'orm' ?
                    "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Tree/Entity" :
                    "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Tree/Document",
                'alias' => 'Gedmo',
                'is_bundle' => false,
            ),
            'NamiCoreBundle' => array(
                'type' => 'annotation',
                'prefix' => 'PhpInk\Nami\CoreBundle\Model\\' . ucfirst($dbAdapter),
                'dir' => 'Model/' . ucfirst($dbAdapter),
                'is_bundle' => true
            )
        );
        if ($dbAdapter === 'orm') {
            $this->container->prependExtensionConfig('doctrine', array(
                'dbal' => array(
                    'default_connection' => 'default',
                    'connections' => array(
                        'default' => array(
                            'driver' =>   $this->container->getParameter('nami_core.database_driver'),
                            'host' =>     $this->container->getParameter('nami_core.database_host'),
                            'port' =>     $this->container->getParameter('nami_core.database_port'),
                            'dbname' =>   $this->container->getParameter('nami_core.database_name'),
                            'user' =>     $this->container->getParameter('nami_core.database_user'),
                            'password' => $this->container->getParameter('nami_core.database_pass'),
                            'charset' => 'UTF8'
                        )
                    )
                ),
                'orm' => array(
                    'default_entity_manager' => 'default',
                    'auto_generate_proxy_classes' => '%kernel.debug%',
                    'entity_managers' => array(
                        'default' => array(
                            'connection' => 'default',
                            'auto_mapping' => false,
                            'mappings' => $mappingsInfo
                        )
                    )
                )
            ));

        } else {
            $mongoHost = $this->container->getParameter('nami_core.database_host').':'.
                         $this->container->getParameter('nami_core.database_port');
            $this->container->prependExtensionConfig('doctrine_mongodb', array(
                'connections' => array(
                    'default' => array(
                        'server' => 'mongodb://' . $mongoHost,
                        'options' => array(
                            'connect' => true
                        )
                    )
                ),
                'default_database' => $this->container->getParameter('nami_core.database_name'),
                'document_managers' => array(
                    'default' => array(
                        'auto_mapping' => false,
                        'mappings' => $mappingsInfo
                    )
                )
            ));
        }
        $adapterKey = ($dbAdapter === 'orm') ? 'orm' : 'mongodb';
        $this->container->prependExtensionConfig('stof_doctrine_extensions', array(
            $adapterKey => array(
                'default' => array(
                    'blameable' => true,
                    'sluggable' => true,
                    'sortable' => true
                )
            )
        ));

        $this->container->prependExtensionConfig('jms_serializer', array(
            'handlers' => array(
                'datetime' => array(
                    'default_format' => 'Y-m-d\TH:i:sO'
                )
            )
        ));
    }

    /**
     * Configures API services
     */
    private function apiConfiguration()
    {
        /*
         * Symfony/Twig Configuration
         */
        $this->container->prependExtensionConfig('framework', array(
            'translator' => array(
                'fallback' => '%nami_core.locale%',
            ),
            'secret' => '%secret%',
            'form' => array(
                'csrf_protection' => false,
            ),
            'validation' => array(
                'enable_annotations' => true,
            ),
            'templating' => array(
                'engines' => ['twig']
            ),
            'default_locale' => '%nami_core.locale%',
            'trusted_proxies' => [],
            'session' => false,
            'fragments' => [],
        ));
        $this->container->prependExtensionConfig('sensio_framework_extra', array(
            'view' => array(
                'annotations' => true,
            )
        ));
        $this->container->prependExtensionConfig('twig', array(
            'debug' => '%kernel.debug%',
            'strict_variables' => '%kernel.debug%',
            'exception_controller' => 'FOS\RestBundle\Controller\ExceptionController::showAction',
            'form_themes' => ['bootstrap_3_layout.html.twig'],
        ));

        /*
         * FOS Rest Configuration
         */
        $this->container->prependExtensionConfig('fos_rest', array(
            'disable_csrf_role' => 'ROLE_API',
            'param_fetcher_listener' => true,
            'view' => array(
                'view_response_listener' => 'force',
                'mime_types' => array(
                    'json' => ['application/json', 'application/json;version=1.0', 'application/json;version=1.1', 'application/json;version=1.2']
                ),
                'formats' => [
                    'json' => true
                ],
                'templating_formats' => [
                    'html' => true
                ]
            ),

            'format_listener' => array(
                'rules' => array(
                    array (
                        'path' => '^/api',
                        'priorities' => array ('json'),
                        'fallback_format' => 'json',
                        'prefer_extension' => false
                    ),
                    array (
                        'path' => '^/',
                        'priorities' => array ('html', 'application/javascript', 'text/css', '*/*'),
                    )
                )
            ),

            'exception' => array(
                'codes' => array(
                    'Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException' => 401,
                    'Symfony\Component\Security\Core\Exception\AuthenticationException' => 401,
                    'Symfony\Component\Security\Core\Exception\UsernameNotFoundException' => 401,
                    'Symfony\Component\Routing\Exception\ResourceNotFoundException' => 404,
                    'Doctrine\ORM\OptimisticLockException' => 'HTTP_CONFLICT'

                ),
                'messages' => array(
                    'Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException' => true,
                    'Symfony\Component\Security\Core\Exception\AuthenticationServiceException' => true,
                    'Symfony\Component\Security\Core\Exception\DisabledException' => true,
                    'Symfony\Component\Security\Core\Exception\AuthenticationException' => true,
                    'Symfony\Component\Routing\Exception\ResourceNotFoundException' => true,
                    'PhpInk\Nami\CoreBundle\Exception\InactiveAccountException' => true,
                    'PhpInk\Nami\CoreBundle\Exception\ItemInactiveException' => true,
                    'PhpInk\Nami\CoreBundle\Exception\TokenNotValidException' => true,
                    'PhpInk\Nami\CoreBundle\Exception\TokenExpiredException' => true,
                )
            ),

            // Listeners
            'allowed_methods_listener' => true,
            'access_denied_listener' => array(
                'json' => true,
            ),
            'body_listener' => array(
                'decoders' => array('json' => 'nami_core.json_decoder'),
            ),
        ));

        /*
         * Lexik JWT Configuration
         */
        $this->container->prependExtensionConfig('lexik_jwt_authentication', [
            'private_key_path' =>    '%nami_core.private_key_path%',
            'public_key_path' =>     '%nami_core.public_key_path%',
            'pass_phrase' =>         '%nami_core.ssh_passphrase%',
            'token_ttl' =>           '%nami_core.token_ttl%',
            'encoder_service' =>     'lexik_jwt_authentication.jwt_encoder',
            'user_identity_field' => 'username',
        ]);

        /*
         * Liip Imagine Configuration
         */
        $this->container->prependExtensionConfig('liip_imagine', [
            'resolvers' => [
                'default' => [
                    'web_path' => [],
                ],
            ],
            'filter_sets' => [
                'cache' => [],
                'preview' => [
                    'quality' => 75,
                    'filters' => [
                        'thumbnail' => [
                            'size' => [125, 125],
                            'mode' => 'outbound',
                            'allow_upscale' => true,
                        ],
                    ],
                ],
                'category' => [
                    'quality' => 75,
                    'filters' => [
                        'thumbnail' => [
                            'size' => [125, 125],
                            'mode' => 'outbound',
                            'allow_upscale' => true,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'nami_core';
    }
}
