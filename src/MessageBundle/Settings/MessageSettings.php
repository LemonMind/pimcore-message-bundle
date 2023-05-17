<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Settings;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

readonly class MessageSettings
{
    public function __construct(
        private ParameterBag $parameterBag
    ) {
    }

    public function getConfig(): array
    {
        $config = $this->parameterBag->get('lemonmind_message');

        return is_array($config) ? $config : [];
    }

    public function getFields(string $class): array
    {
        $config = $this->getConfig();
        $fields = explode(',', $config[$class]['fields_to_send']);

        return is_array($fields) ? $fields : [];
    }
}
