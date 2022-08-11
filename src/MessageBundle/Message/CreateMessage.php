<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Message;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\TexterInterface;

class CreateMessage
{
    private string $seletedChatter;
    private string $class;
    private string $additionalInfo;
    private AbstractObject $product;
    private array $fields;
    private array $config;
    private ChatterInterface $chatter;
    private TexterInterface $texter;

    public function __construct(string $seletedChatter, AbstractObject $product, string $class, array $fields, string $additionalInfo, array $config, ChatterInterface $chatter, TexterInterface $texter)
    {
        $this->seletedChatter = $seletedChatter;
        $this->class = $class;
        $this->additionalInfo = $additionalInfo;
        $this->product = $product;
        $this->fields = $fields;
        $this->config = $config;
        $this->chatter = $chatter;
        $this->texter = $texter;
    }

    public function getSeletedChatter(): string
    {
        return $this->seletedChatter;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }

    public function getProduct(): AbstractObject
    {
        return $this->product;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getChatter(): ChatterInterface
    {
        return $this->chatter;
    }

    public function getTexter(): TexterInterface
    {
        return $this->texter;
    }
}
