<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Model;

use Pimcore\Model\DataObject\AbstractObject;

class EmailMessageModel extends AbstractMessageModel
{
    public function create(): string
    {
        if ($this->product instanceof AbstractObject) {
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
            $html .= '</table>';

            if ('' !== $this->additionalInfo) {
                $html .= '<br>' . $this->additionalInfo;
            }
        } else {
            $html = 'Error creating message';
        }

        return $html;
    }
}
