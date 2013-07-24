<?php

namespace RedCode\InstagramFeedBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('redcode_instagram');

        $rootNode
            ->children()
                ->scalarNode('tag_class')->end()
                ->scalarNode('image_class')->end()
                ->scalarNode('client_id')->end()
                ->scalarNode('page_items')->defaultValue(10)->end()
                ->scalarNode('start_from')->defaultValue(date('Y-m-d'))->end()
                ->arrayNode('approve_rules')
                    ->children()
                        ->arrayNode('by_user')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('by_date')->cannotBeEmpty()
                            ->children()
                                ->scalarNode('from')->end()
                                ->scalarNode('to')->end()
                            ->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
