<?php

namespace LemonMind\MessageBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('lemon_mind_message');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('class_to_send')->end()
            ->scalarNode('fields_to_send')->end()
            ->scalarNode('email_to_send')->end()
            ->scalarNode('sms_to')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
