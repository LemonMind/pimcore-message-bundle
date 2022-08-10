<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use LemonMind\MessageBundle\Model\AbstractMessageModel;
use LemonMind\MessageBundle\Model\DiscordMessageModel;
use LemonMind\MessageBundle\Model\EmailMessageModel;
use LemonMind\MessageBundle\Model\GoogleChatMessageModel;
use LemonMind\MessageBundle\Model\SlackMessageModel;
use LemonMind\MessageBundle\Model\SmsMessageModel;
use LemonMind\MessageBundle\Model\TelegramMessageModel;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Mail;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/chatter")
 */
class ChatterController extends AdminController
{
    private bool $success = true;

    /**
     * @Route("/send-notification/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(Request $request, int $id, ChatterInterface $chatter, TexterInterface $texter, ContainerInterface $container): Response
    {
        \Pimcore::unsetAdminMode();

        $class = $container->getParameter('lemonmind_message.class_to_send');

        if (class_exists($class)) {
            $product = $class::getById($id);
        } else {
            $this->success = false;

            return $this->json(
                [
                    'success' => $this->success,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($product instanceof AbstractObject) {
            $product::setGetInheritedValues(true);

            $fields = explode(',', $container->getParameter('lemonmind_message.fields_to_send'));
            $additionalInfo = $request->get('additionalInfo');

            switch ($request->get('chatter')) {
                case 'discord':
                    $message = new DiscordMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message, $chatter);

                    break;

                case 'googlechat':
                    $message = new GoogleChatMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message, $chatter);

                    break;

                case 'slack':
                    $message = new SlackMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message, $chatter);

                    break;

                case 'telegram':
                    $message = new TelegramMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message, $chatter);

                    break;

                case 'email':
                    $emailTo = $container->getParameter('lemonmind_message.email_to_send');

                    if (!is_string($emailTo)) {
                        $emailTo = '';
                    }
                    $emailMessage = new EmailMessageModel($product, $fields, $additionalInfo);
                    $this->email($emailMessage, $emailTo);

                    break;
                case 'sms':
                    $smsTo = $container->getParameter('lemonmind_message.sms_to');

                    if (!is_string($smsTo)) {
                        $smsTo = '';
                    }
                    $message = new SmsMessageModel($product, $fields, $additionalInfo, $smsTo);
                    $this->sms($message, $texter);

                    break;
                default:
                    $this->success = false;
            }

            if ($this->success) {
                return $this->json(
                    [
                        'success' => $this->success,
                    ],
                    Response::HTTP_OK
                );
            } else {
                return $this->json(
                    [
                        'success' => $this->success,
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } else {
            $this->success = false;

            return $this->json(
                [
                    'success' => $this->success,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/class")
     */
    public function classAction(ContainerInterface $container): Response
    {
        $class = $container->getParameter('lemonmind_message.class_to_send');

        return $this->json(
            [
                'class_to_send' => $class,
            ],
            Response::HTTP_OK
        );
    }

    private function sendMessage(AbstractMessageModel $message, ChatterInterface $chatter): void
    {
        try {
            $chatter->send($message->create());
        } catch (TransportExceptionInterface $e) {
            $this->success = false;
        }
    }

    public function email(EmailMessageModel $emailMessage, string $emailTo): void
    {
        try {
            $mail = new Mail();
            $mail->to($emailTo);
            $mail->setSubject($emailMessage->subject());
            $mail->html($emailMessage->body());
            $mail->send();
        } catch (TransportExceptionInterface $e) {
            $this->success = false;
        }
    }

    public function sms(SmsMessageModel $smsMessage, TexterInterface $texter): void
    {
        try {
            $texter->send($smsMessage->create());
        } catch (TransportExceptionInterface $e) {
            $this->success = false;
        }
    }
}
