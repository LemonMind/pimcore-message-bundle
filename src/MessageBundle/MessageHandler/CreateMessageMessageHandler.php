<?php

namespace LemonMind\MessageBundle\MessageHandler;

use LemonMind\MessageBundle\Message\CreateMessage;
use LemonMind\MessageBundle\Model\AbstractMessageModel;
use LemonMind\MessageBundle\Model\DiscordMessageModel;
use LemonMind\MessageBundle\Model\EmailMessageModel;
use LemonMind\MessageBundle\Model\GoogleChatMessageModel;
use LemonMind\MessageBundle\Model\SlackMessageModel;
use LemonMind\MessageBundle\Model\SmsMessageModel;
use LemonMind\MessageBundle\Model\TelegramMessageModel;
use Pimcore\Mail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\TexterInterface;

class CreateMessageMessageHandler implements MessageHandlerInterface
{
    public function __invoke(CreateMessage $createMessage)
    {

        $mail = new Mail();
        $mail->to("some@email.com");
        $mail->setSubject("tesarease");
        $mail->html('body');
        $mail->send();
//        $config = $createMessage->getConfig();
//        switch ($createMessage->getSeletedChatter()) {
//            case 'discord':
//                $message = new DiscordMessageModel($createMessage->getProduct(), $createMessage->getFields(), $createMessage->getAdditionalInfo());
//                $this->sendMessage($message, $createMessage->getChatter());
//
//                break;
//
//            case 'googlechat':
//                $message = new GoogleChatMessageModel($createMessage->getProduct(), $createMessage->getFields(), $createMessage->getAdditionalInfo());
//                $this->sendMessage($message, $createMessage->getChatter());
//
//                break;
//
//            case 'slack':
//                $message = new SlackMessageModel($createMessage->getProduct(), $createMessage->getFields(), $createMessage->getAdditionalInfo());
//                $this->sendMessage($message, $createMessage->getChatter());
//
//                break;
//
//            case 'telegram':
//                $message = new TelegramMessageModel($createMessage->getProduct(), $createMessage->getFields(), $createMessage->getAdditionalInfo());
//                $this->sendMessage($message, $createMessage->getChatter());
//
//                break;
//
//            case 'email':
//                if (!isset($config[$createMessage->getClass()]['email_to_send'])) {
//                    throw new \Exception('email_to_send must be defined in lemonmind_message config for class ' . $createMessage->getClass());
//                }
//
//                $emailTo = $config[$createMessage->getClass()]['email_to_send'];
//                $emailMessage = new EmailMessageModel($createMessage->getProduct(), $createMessage->getFields(), $createMessage->getAdditionalInfo());
//                $this->email($emailMessage, $emailTo);
//
//                break;
//            case 'sms':
//                if (!isset($config[$createMessage->getClass()]['sms_to'])) {
//                    throw new \Exception('sms_to must be defined in lemonmind_message config for class ' . $createMessage->getClass());
//                }
//
//                $smsTo = (string)$config[$createMessage->getClass()]['sms_to'];
//                $message = new SmsMessageModel($createMessage->getProduct(), $createMessage->getFields(), $createMessage->getAdditionalInfo(), $smsTo);
//                $this->sms($message, $createMessage->getTexter());
//
//                break;
//            default:
//        }

    }

    private function sendMessage(AbstractMessageModel $message, ChatterInterface $chatter)
    {
        try {
            $chatter->send($message->create());
        } catch (TransportExceptionInterface $e) {
            $success = false;
        }
    }

    private function email(EmailMessageModel $message, string $emailTo)
    {
        try {
            $mail = new Mail();
            $mail->to($emailTo);
            $mail->setSubject($message->subject());
            $mail->html($message->body());
            $mail->send();
        } catch (TransportExceptionInterface $e) {
            $success = false;
        }
    }

    private function sms(SmsMessageModel $message, TexterInterface $texter)
    {
        try {
            $texter->send($message->create());
        } catch (TransportExceptionInterface $e) {
            $success = false;
        }
    }
}
