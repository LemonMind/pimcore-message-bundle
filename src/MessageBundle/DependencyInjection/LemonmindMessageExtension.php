<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LemonmindMessageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $lemonmindMessageConfig = [];

        $lemonmindMessageConfig['allowed_chatters'] = $config['allowed_chatters'];

        foreach ($config['classes'] as $keys => $values) {
            $arr = [];

            foreach ($values as $key => $value) {
                $arr[$key] = $value;
            }
            $lemonmindMessageConfig[$keys] = $arr;
        }

        $container->setParameter('lemonmind_message', $lemonmindMessageConfig);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
