<?php

namespace LemonMind\MessageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LemonMindMessageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        if (isset($config['class_to_send'])) {
            $container->setParameter('lemon_mind_message.class_to_send', $config['class_to_send']);
        }

        if (isset($config['fields_to_send'])) {
            $container->setParameter('lemon_mind_message.fields_to_send', $config['fields_to_send']);
        }

        if (isset($config['email_to_send'])) {
            $container->setParameter('lemon_mind_message.email_to_send', $config['email_to_send']);
        }

        if (isset($config['sms_to'])) {
            $container->setParameter('lemon_mind_message.sms_to', (string)$config['sms_to']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
