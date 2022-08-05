<?php

namespace LemonMind\MessageBundle\Model;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackContextBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class SlackMessageModel extends AbstractMessageModel
{
    private const MAX_FIELDS = 5;

    public function create(): ChatMessage
    {
        if ($this->product instanceof AbstractObject) {
            $chatMessage = new ChatMessage('Object ' . $this->product->getId());

            $options = (new SlackOptions())
                ->block((new SlackHeaderBlock("Object id " . $this->product->getId())))
                ->block(new SlackDividerBlock());

            $infoBlock = new SlackSectionBlock();
            $current = 0;

            foreach ($this->fields as $field) {
                $data = $this->product->get($field);
                if (null === $data) {
                    continue;
                }

                $infoBlock->field("*$field*");
                $infoBlock->field(is_scalar($data) ? $data : $data->getName());
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
        } else {
            $chatMessage = new ChatMessage("Error creating message");
        }

        return $chatMessage;
    }

}
