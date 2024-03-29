<?php

namespace Welp\BatchBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('welp_batch');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode->children()
            ->scalarNode('entity_manager')->defaultNull()->end()
            ->enumNode('broker_type')->values(array('rabbitmq','redis'))->defaultValue('rabbitmq')->end()
            ->scalarNode('broker_connection')->defaultValue('default')->end()
            ->scalarNode('batch_results_folder')->defaultValue('.')->end()
            ->arrayNode('batch_entity')
                ->children()
                    ->scalarNode('batch')->defaultValue('')->end()
                ->end()
            ->end()
            ->arrayNode('manage_entities')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('entity_name')->defaultValue('')->end()
                        ->scalarNode('form_name')->defaultValue('')->end()
                        ->integerNode('batch_size')->defaultValue(0)->end()
                        ->arrayNode('actions')
                            ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
