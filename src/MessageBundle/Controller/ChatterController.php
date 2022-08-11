<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use Exception;
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

    /**
     * @Route("/send-notification/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(Request $request, int $id, ContainerInterface $container): Response
    {
        \Pimcore::unsetAdminMode();

        $class = $request->get('classToSend');

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
            $config = $container->getParameter('lemonmind_message');

            $fields = explode(',', $config[$class]['fields_to_send']);
            $additionalInfo = $request->get('additionalInfo');

            switch ($request->get('chatter')) {
                case 'discord':
                    $message = new DiscordMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message);

                    break;

                case 'googlechat':
                    $message = new GoogleChatMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message);

                    break;

                case 'slack':
                    $message = new SlackMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message);

                    break;

                case 'telegram':
                    $message = new TelegramMessageModel($product, $fields, $additionalInfo);
                    $this->sendMessage($message);

                    break;

                case 'email':
                    if (!isset($config[$class]['email_to_send'])) {
                        throw new \Exception('email_to_send must be defined in lemonmind_message config for class ' . $class);
                    }

                    $emailTo = $config[$class]['email_to_send'];
                    $emailMessage = new EmailMessageModel($product, $fields, $additionalInfo);
                    $this->email($emailMessage, $emailTo);

                    break;
                case 'sms':
                    if (!isset($config[$class]['sms_to'])) {
                        throw new \Exception('sms_to must be defined in lemonmind_message config for class ' . $class);
                    }

                    $smsTo = (string) $config[$class]['sms_to'];
                    $message = new SmsMessageModel($product, $fields, $additionalInfo, $smsTo);
                    $this->sms($message);

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
        $classes = [];
        $config = $container->getParameter('lemonmind_message');

        foreach ($config as $key => $value) {
            $classes[] = $key;
        }

        return $this->json(
            [
                'classes' => $classes,
            ],
            Response::HTTP_OK
        );
    }

    private function sendMessage(AbstractMessageModel $message): void
    {
        if (is_null($this->chatter)) {
            $this->success = false;

            throw new Exception('chatter service not provided');
        }

        try {
            $this->chatter->send($message->create());
        } catch (TransportExceptionInterface $e) {
            $this->success = false;
        } finally {
            $this->chatter = null;
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

    public function sms(SmsMessageModel $smsMessage): void
    {
        if (is_null($this->texter)) {
            $this->success = false;

            throw new Exception('texter service not provided');
        }

        try {
            $this->texter->send($smsMessage->create());
        } catch (TransportExceptionInterface $e) {
            $this->success = false;
        } finally {
            $this->texter = null;
        }
    }
}
