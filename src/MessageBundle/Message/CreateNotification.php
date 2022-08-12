<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Message;

class CreateNotification
{
    private string $selectedChatter;
    private string $class;
    private string $additionalInfo;
    private int $productId;
    private array $fields;
    private array $config;

    public function __construct(string $selectedChatter, int $productId, string $class, array $fields, string $additionalInfo, array $config)
    {
        $this->selectedChatter = $selectedChatter;
        $this->class = $class;
        $this->additionalInfo = $additionalInfo;
        $this->productId = $productId;
        $this->fields = $fields;
        $this->config = $config;
    }

    public function getSelectedChatter(): string
    {
        return $this->selectedChatter;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
