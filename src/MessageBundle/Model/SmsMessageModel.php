<?php

namespace LemonMind\MessageBundle\Model;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Notifier\Message\SmsMessage;

class SmsMessageModel
{
    private object $product;
    private array $fields;
    private string $additionalInfo;
    private string $smsTo;

    public function __construct(object $product, array $fields, string $additionalInfo, string $smsTo)
    {
        $this->product = $product;
        $this->fields = $fields;
        $this->additionalInfo = $additionalInfo;
        $this->smsTo = $smsTo;
    }

    public function create(): SmsMessage
    {
        if ($this->product instanceof AbstractObject) {
            $smsBody = 'Object id: ' . $this->product->getId();
            foreach ($this->fields as $field) {
                $data = $this->product->get($field);
                if (null === $data) {
                    continue;
                }
                $smsBody .= " $field: ";
                $smsBody .= is_scalar($data) ? $data : $data->getName();
            }

            if ($this->additionalInfo !== '') {
                $smsBody .= " Additional information: $this->additionalInfo";
            }

            $sms = new SmsMessage("+$this->smsTo", $smsBody);
            $sms->transport('smsapi');
        } else {
            $sms = new SmsMessage("+$this->smsTo", "Error creating message");
        }
        return $sms;
    }
}
