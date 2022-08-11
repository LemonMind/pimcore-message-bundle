<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lemonmind_message');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('allowed_chatters')->end()
                ->arrayNode('classes')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('fields_to_send')->end()
                        ->scalarNode('email_to_send')->end()
                        ->scalarNode('sms_to')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
