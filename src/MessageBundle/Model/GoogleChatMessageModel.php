<?php

namespace LemonMind\MessageBundle\Model;

use Symfony\Component\Notifier\Bridge\GoogleChat\GoogleChatOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class GoogleChatMessageModel
{
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

        $header = ['title' => 'Object ' . $this->product->getName() . ' id ' . $this->product->getId()];
        $sections = [];
        $widgets = [];

        foreach ($this->fields as $field) {
            $data = $this->product->get($field);
            if (null === $data) {
                continue;
            }
            $keyValue = [];
            $keyValue['topLabel'] = $field;
            $keyValue['content'] = is_scalar($data) ? $data : $data->getName();
            $widgets[]['keyValue'] = $keyValue;
        }

        $sections[]['widgets'] = $widgets;

        if ($this->additionalInfo !== '') {
            $widgets = [];
            $textParagraph = [];
            $headerParagraph['text'] = 'Additional information';
            $textParagraph['text'] = $this->additionalInfo;
            $widgets[]['textParagraph'] = $headerParagraph;
            $widgets[]['textParagraph'] = $textParagraph;
            $sections[]['widgets'] = $widgets;
        }

        $options = new GoogleChatOptions();
        $options
            ->card(
                [
                    'header' => $header,
                    'sections' => $sections
                ]
            );

        $chatMessage->options($options);
        $chatMessage->transport('googlechat');

        return $chatMessage;
    }
}
