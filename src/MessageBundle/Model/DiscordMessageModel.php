<?php

namespace LemonMind\MessageBundle\Model;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Message\ChatMessage;

class DiscordMessageModel extends AbstractMessageModel
{
    public function create(): ChatMessage
    {
        if ($this->product instanceof AbstractObject) {
            $chatMessage = new ChatMessage('');

            $options = new DiscordOptions();
            $embed = new DiscordEmbed();
            $embed->title("Object id " . $this->product->getId());

            foreach ($this->fields as $field) {
                $data = $this->product->get($field);
                if (null === $data) {
                    continue;
                }

                $discordField = new DiscordFieldEmbedObject();
                $discordField->name($field);
                $discordField->value(is_scalar($data) ? $data : $data->getName());
                $embed->addField($discordField);
            }
            if ($this->additionalInfo !== '') {
                $discordField = new DiscordFieldEmbedObject();
                $discordField->name("Additional information");
                $discordField->value($this->additionalInfo);
                $embed->addField($discordField);
            }

            $options->addEmbed($embed);
            $chatMessage->options($options);
            $chatMessage->transport('discord');
        } else {
            $chatMessage = new ChatMessage('Error creating message');
        }
        return $chatMessage;
    }
}
