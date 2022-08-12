<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use Exception;
use LemonMind\MessageBundle\Message\CreateNotification;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/chatter")
 */
class ChatterController extends AdminController
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

    /**
     * @Route("/send-notification/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(Request $request, int $id, ContainerInterface $container, MessageBusInterface $bus): Response
    {
        \Pimcore::unsetAdminMode();

        $class = $request->get('classToSend');

        if (!class_exists($class)) {
            return $this->returnAction(false);
        }

        $product = $class::getById($id);

        if (!$product instanceof AbstractObject) {
            return $this->returnAction(false);
        }
        $product::setGetInheritedValues(true);
        $config = $container->getParameter('lemonmind_message');

        $fields = explode(',', $config[$class]['fields_to_send']);
        $additionalInfo = $request->get('additionalInfo');

        try {
            $bus->dispatch(new CreateNotification($request->get('chatter'), $id, $class, $fields, $additionalInfo, $config));
        } catch (Exception $e) {
            $this->returnAction(false);
        }

        return $this->returnAction(true);
    }

    /**
     * @Route("/class")
     */
    public function classAction(ContainerInterface $container): Response
    {
        $classes = [];
        $config = $container->getParameter('lemonmind_message');

        foreach ($config as $key => $value) {
            if ('allowed_chatters' === $key) {
                continue;
            }
            $classes[] = $key;
        }

        return $this->json(
            [
                'classes' => $classes,
                'allowed_chatters' => $config['allowed_chatters'],
            ],
            Response::HTTP_OK
        );
    }

    public function returnAction(bool $success): Response
    {
        return $this->json(
            [
                'success' => $success,
            ],
            $success ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
