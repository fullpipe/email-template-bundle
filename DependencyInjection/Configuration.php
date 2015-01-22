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
                        ->scalarNode('utm_source')->end()
                        ->scalarNode('utm_medium')->end()
                        ->scalarNode('utm_campaign')->end()
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
                                ->treatNullLike(array())
                                ->children()
                                    ->scalarNode('utm_source')->end()
                                    ->scalarNode('utm_medium')->end()
                                    ->scalarNode('utm_campaign')->end()
                                ->end()
                            ->end()
                            ->scalarNode('host')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->booleanNode('generate_text_vertion')
                                ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
