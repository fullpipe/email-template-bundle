<?php

namespace Fullpipe\EmailTemplateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fullpipe_email_template');

        $rootNode
            ->children()
                ->arrayNode('utm')
                    ->children()
                        ->scalarNode('source')->end()
                        ->scalarNode('medium')->end()
                        ->scalarNode('campaign')->end()
                    ->end()
                ->end()
                ->scalarNode('host')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('templates')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('utm')
                                ->children()
                                    ->scalarNode('source')->end()
                                    ->scalarNode('medium')->end()
                                    ->scalarNode('campaign')->end()
                                ->end()
                            ->end()
                            ->scalarNode('host')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
