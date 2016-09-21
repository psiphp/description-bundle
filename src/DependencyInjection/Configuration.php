<?php

namespace Psi\Bundle\Description\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('psi_description');
        $rootNode->addDefaultsIfNotSet();
        $rootNode->children()
            ->arrayNode('enhancers')
                ->info('Enabled description enhancers')
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('schema')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')
                        ->info('Enable schema to validate the description. (Can be disabled in prod environment)')
                        ->defaultValue(true)
                    ->end()
                    ->arrayNode('extensions')
                        ->info('Enabled schema extensions')
                        ->defaultValue(['std'])
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
