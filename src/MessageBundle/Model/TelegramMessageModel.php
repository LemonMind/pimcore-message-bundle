<?php

namespace LemonMind\MessageBundle\Model;

use Symfony\Component\Notifier\Message\ChatMessage;

class TelegramMessageModel extends AbstractMessageModel
{

    public function create(): ChatMessage
    {
        $subject = "Object " . $this->product->getName() . ' id ' . $this->product->getId();
        foreach ($this->fields as $field) {
            $data = $this->product->get($field);
            if (null === $data) {
                continue;
            }
            $subject .= "\n$field: ";
            $subject .= is_scalar($data) ? $data : $data->getName();
        }
        if ($this->additionalInfo !== '') {
            $subject .= "\n\nAdditional information";
            $subject .= "\n$this->additionalInfo";
        }

        $chatMessage = new ChatMessage("$subject");
        $chatMessage->transport('telegram');
        return $chatMessage;
    }
}
