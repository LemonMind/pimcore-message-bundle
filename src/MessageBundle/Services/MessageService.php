<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Services;

use Exception;
use LemonMind\MessageBundle\Model\AbstractMessageModel;
use LemonMind\MessageBundle\Model\DiscordMessageModel;
use LemonMind\MessageBundle\Model\EmailMessageModel;
use LemonMind\MessageBundle\Model\GoogleChatMessageModel;
use LemonMind\MessageBundle\Model\SlackMessageModel;
use LemonMind\MessageBundle\Model\SmsMessageModel;
use LemonMind\MessageBundle\Model\TelegramMessageModel;
use Pimcore\Mail;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\TexterInterface;

class MessageService
{
    public static function create(string $request, AbstractObject $product, string $class, array $fields, string $additionalInfo, array $config, ?ChatterInterface $chatter, ?TexterInterface $texter): bool
    {
        $success = false;

        switch ($request) {
            case 'discord':
                $message = new DiscordMessageModel($product, $fields, $additionalInfo);
                $success = self::sendMessage($message, $chatter);

                break;

            case 'googlechat':
                $message = new GoogleChatMessageModel($product, $fields, $additionalInfo);
                $success = self::sendMessage($message, $chatter);

                break;

            case 'slack':
                $message = new SlackMessageModel($product, $fields, $additionalInfo);
                $success = self::sendMessage($message, $chatter);

                break;

            case 'telegram':
                $message = new TelegramMessageModel($product, $fields, $additionalInfo);
                $success = self::sendMessage($message, $chatter);

                break;

            case 'email':
                if (!isset($config[$class]['email_to_send'])) {
                    throw new \Exception('email_to_send must be defined in lemonmind_message config for class ' . $class);
                }

                $emailTo = $config[$class]['email_to_send'];
                $emailMessage = new EmailMessageModel($product, $fields, $additionalInfo);
                $success = self::email($emailMessage, $emailTo);

                break;
            case 'sms':
                if (!isset($config[$class]['sms_to'])) {
                    throw new \Exception('sms_to must be defined in lemonmind_message config for class ' . $class);
                }

                $smsTo = (string) $config[$class]['sms_to'];
                $message = new SmsMessageModel($product, $fields, $additionalInfo, $smsTo);
                $success = self::sms($message, $texter);

                break;
            default:
                $success = false;
        }

        return $success;
    }

    private static function sendMessage(AbstractMessageModel $message, ChatterInterface $chatter): bool
    {
        if (is_null($chatter)) {
            throw new Exception('texter service not provided');
        }

        try {
            $chatter->send($message->create());
        } catch (TransportExceptionInterface $e) {
            return false;
        }

        return true;
    }

    public static function email(EmailMessageModel $emailMessage, string $emailTo): bool
    {
        try {
            $mail = new Mail();
            $mail->to($emailTo);
            $mail->setSubject($emailMessage->subject());
            $mail->html($emailMessage->body());
            $mail->send();
        } catch (TransportExceptionInterface $e) {
            return false;
        }

        return true;
    }

    public static function sms(SmsMessageModel $smsMessage, TexterInterface $texter): bool
    {
        if (is_null($texter)) {
            throw new Exception('texter service not provided');
        }

        try {
            $texter->send($smsMessage->create());
        } catch (TransportExceptionInterface $e) {
            return false;
        }

        return true;
    }
}
