<?php

namespace LemonMind\MessageBundle\Model;


class EmailMessageModel
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

    public function create(): string
    {
        $html = '<table>';
        foreach ($this->fields as $field) {
            $data = $this->product->get($field);
            if (null === $data) {
                continue;
            }
            $html .= '<tr>';
            $html .= '<td>' . $field . '</td>';
            $html .= '<td>' . (is_scalar($data) ? $data : $data->getName()) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tr>';
        $html .= '</table>';
        if ($this->additionalInfo !== '') {
            $html .= '<br>' . $this->additionalInfo;
        }
        return $html;
    }

}
