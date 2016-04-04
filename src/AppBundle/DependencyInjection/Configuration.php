<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $rootNode
            ->children()
                ->arrayNode('workflow')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('marking_store')
                                ->isRequired()
                                ->children()
                                    ->enumNode('type')
                                        ->values(['property_accessor', 'scalar'])
                                    ->end()
                                    ->arrayNode('arguments')
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(function ($v) { return [$v]; })
                                        ->end()
                                        ->prototype('scalar')
                                        ->end()
                                    ->end()
                                    ->scalarNode('service')
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                                ->validate()
                                    ->always(function ($v) {
                                        if (isset($v['type']) && isset($v['service'])) {
                                            throw new \InvalidArgumentException('"type" and "service" could not be used together.');
                                        }

                                        return $v;
                                    })
                                ->end()
                            ->end()
                            ->arrayNode('supports')
                                ->isRequired()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function ($v) { return [$v]; })
                                ->end()
                                ->prototype('scalar')
                                    ->cannotBeEmpty()
                                    ->validate()
                                        ->ifTrue(function ($v) { return !class_exists($v); })
                                        ->thenInvalid('The supported class %s does not exist.')
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('places')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->prototype('scalar')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                            ->arrayNode('transitions')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->prototype('array')
                                    ->children()
                                        ->arrayNode('from')
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(function ($v) { return [$v]; })
                                            ->end()
                                            ->requiresAtLeastOneElement()
                                            ->prototype('scalar')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                        ->arrayNode('to')
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(function ($v) { return [$v]; })
                                            ->end()
                                            ->requiresAtLeastOneElement()
                                            ->prototype('scalar')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
