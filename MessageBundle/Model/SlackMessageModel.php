<?php

namespace LemonMind\MessageBundle\Model;

use Symfony\Component\Notifier\Bridge\Slack\Block\SlackContextBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class SlackMessageModel
{
    private const MAX_FIELDS = 5;

    private object $product;
    private array $fields;
    private string $additionalInfo;

    public function __construct(object $product, array $fields, string $additionalInfo)
    {
        $this->product = $product;
        $this->fields = $fields;
        $this->additionalInfo = $additionalInfo;
    }

    public function create(): ChatMessage
    {
        $chatMessage = new ChatMessage('Object ' . $this->product->getId());

        $options = (new SlackOptions())
            ->block((new SlackHeaderBlock("Object " . $this->product->getName() . ' id ' . $this->product->getId())))
            ->block(new SlackDividerBlock());

        $infoBlock = new SlackSectionBlock();
        $current = 0;

        foreach ($this->fields as $field) {
            if (null === $this->product->get($field)) {
                continue;
            }

            $infoBlock->field("*$field*");
            $infoBlock->field($this->product->get($field));
            $current++;
            if ($current === self::MAX_FIELDS) {
                $options->block(($infoBlock));
                $infoBlock = new SlackSectionBlock();
                $current = 0;
            }

        }

        if ($current !== 0 && $current < self::MAX_FIELDS) {
            $options->block(($infoBlock));
        }
        $options->block(new SlackDividerBlock());

        if ($this->additionalInfo !== '') {
            $options->block((new SlackContextBlock())->text($this->additionalInfo));
            $options->block(new SlackDividerBlock());
        }

        $chatMessage->options($options);
        $chatMessage->transport('slack');

        return $chatMessage;
    }

}
