<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Model;

use Symfony\Component\Notifier\Message\ChatMessage;

abstract class AbstractMessageModel
{
    protected object $product;
    protected array $fields;
    protected string $additionalInfo;

    public function __construct(object $product, array $fields, string $additionalInfo)
    {
        $this->product = $product;
        $this->fields = $fields;
        $this->additionalInfo = $additionalInfo;
    }

    abstract public function create(): ChatMessage;
}
