<?php

namespace PhpInk\Nami\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nami_core');
        $rootNode
            ->children()
                ->scalarNode('database_adapter')->defaultValue('odm')->end()
                ->scalarNode('database_host')->defaultValue('localhost')->end()
                ->scalarNode('database_port')->defaultValue('27017')->end()
                ->scalarNode('database_name')->defaultValue('nami')->end()
                ->scalarNode('database_user')->defaultValue(null)->end()
                ->scalarNode('database_pass')->defaultValue(null)->end()
                ->scalarNode('locale')->defaultValue('fr')->end()
                ->scalarNode('upload_dir')->defaultValue('/media/upload')->end()
                ->scalarNode('plugin_path')->defaultValue('%kernel.root_dir%/../plugins')->end()
                ->scalarNode('cache_host')->defaultValue('localhost')->end()
                ->integerNode('cache_port')->defaultValue(27017)->end()
                ->scalarNode('mailer_to')->defaultValue('admin@localhost')->end()
                ->scalarNode('mailer_from')->defaultValue('no-reply@localhost')->end()
                ->scalarNode('mailer_transport')->defaultValue('smtp')->end()
                ->scalarNode('mailer_host')->defaultValue('localhost')->end()
                ->integerNode('mailer_port')->defaultValue(25)->end()
                ->scalarNode('mailer_username')->defaultValue(null)->end()
                ->scalarNode('mailer_password')->defaultValue(null)->end()
                ->scalarNode('mailer_encryption')->defaultValue(null)->end()
                ->scalarNode('public_key_path')->defaultValue('%kernel.root_dir%/../vendor/phpink/nami-core-bundle/Resources/config/jwt/public.pem')->end()
                ->scalarNode('private_key_path')->defaultValue('%kernel.root_dir%/../vendor/phpink/nami-core-bundle/Resources/config/jwt/private.pem')->end()
                ->scalarNode('ssh_passphrase')->defaultValue('')->end()
                ->integerNode('token_ttl')->defaultValue(86400)->end()
                ->integerNode('reset_token_ttl')->defaultValue(86400)->end()
                ->scalarNode('front_url')->defaultValue('%host%')->end()
                ->scalarNode('front_url_confirmation')->defaultValue('%nami_api.front_url%/user/confirm/{token}')->end()
                ->scalarNode('front_url_resetting')->defaultValue('%nami_api.front_url%/user/reset/{token}')->end()
            ->end();

        return $treeBuilder;
    }
}
