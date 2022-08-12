<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\MessageHandler;

use LemonMind\MessageBundle\Message\CreateNotification;
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

class CreateNotificationMessageHandler implements MessageHandlerInterface
{
    protected ?ChatterInterface $chatter = null;
    protected ?TexterInterface $texter = null;

    public function setTexter(mixed $texter): void
    {
        $this->texter = $texter;
    }

    public function setChatter(mixed $chatter): void
    {
        $this->chatter = $chatter;
    }

    public function __invoke(CreateNotification $createMessage): void
    {
        $config = $createMessage->getConfig();
        $product = $createMessage->getClass()::getById($createMessage->getProductId());

        switch ($createMessage->getSelectedChatter()) {
            case 'discord':
                $message = new DiscordMessageModel($product, $createMessage->getFields(), $createMessage->getAdditionalInfo());

                if (null !== $this->chatter) {
                    $this->sendMessage($message, $this->chatter);
                }

                break;

            case 'googlechat':
                $message = new GoogleChatMessageModel($product, $createMessage->getFields(), $createMessage->getAdditionalInfo());

                if (null !== $this->chatter) {
                    $this->sendMessage($message, $this->chatter);
                }

                break;

            case 'slack':
                $message = new SlackMessageModel($product, $createMessage->getFields(), $createMessage->getAdditionalInfo());

                if (null !== $this->chatter) {
                    $this->sendMessage($message, $this->chatter);
                }

                break;

            case 'telegram':
                $message = new TelegramMessageModel($product, $createMessage->getFields(), $createMessage->getAdditionalInfo());

                if (null !== $this->chatter) {
                    $this->sendMessage($message, $this->chatter);
                }

                break;

            case 'email':
                if (!isset($config[$createMessage->getClass()]['email_to_send'])) {
                    throw new \Exception('email_to_send must be defined in lemonmind_message config for class ' . $createMessage->getClass());
                }

                $emailTo = $config[$createMessage->getClass()]['email_to_send'];
                $emailMessage = new EmailMessageModel($product, $createMessage->getFields(), $createMessage->getAdditionalInfo());
                $this->email($emailMessage, $emailTo);

                break;
            case 'sms':
                if (!isset($config[$createMessage->getClass()]['sms_to'])) {
                    throw new \Exception('sms_to must be defined in lemonmind_message config for class ' . $createMessage->getClass());
                }

                $smsTo = (string) $config[$createMessage->getClass()]['sms_to'];
                $message = new SmsMessageModel($product, $createMessage->getFields(), $createMessage->getAdditionalInfo(), $smsTo);

                if (null !== $this->texter) {
                    $this->sms($message, $this->texter);
                }

                break;
            default:
                throw new \Exception('Error getting chatter');
        }
    }

    public function sendMessage(AbstractMessageModel $message, ChatterInterface $chatter): void
    {
        try {
            $chatter->send($message->create());
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function email(EmailMessageModel $message, string $emailTo): void
    {
        try {
            $mail = new Mail();
            $mail->to($emailTo);
            $mail->setSubject($message->subject());
            $mail->html($message->body());
            $mail->send();
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sms(SmsMessageModel $message, TexterInterface $texter): void
    {
        try {
            $texter->send($message->create());
        } catch (TransportExceptionInterface $e) {
        }
    }
}
