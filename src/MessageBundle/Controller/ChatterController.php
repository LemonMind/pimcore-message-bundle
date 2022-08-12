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
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/chatter")
 */
class ChatterController extends AdminController
{
    /**
     * @Route("/send-notification/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(Request $request, int $id, ContainerInterface $container, MessageBusInterface $bus): Response
    {
        \Pimcore::unsetAdminMode();

        $class = $request->get('classToSend');

        if (!class_exists($class)) {
            return $this->returnAction(false, 'Class does not exist');
        }

        $product = $class::getById($id);

        if (!$product instanceof AbstractObject) {
            return $this->returnAction(false, 'Product is not an instance of AbstractObject');
        }
        $product::setGetInheritedValues(true);
        $config = $container->getParameter('lemonmind_message');

        $fields = explode(',', $config[$class]['fields_to_send']);
        $additionalInfo = $request->get('additionalInfo');

        try {
            $bus->dispatch(new CreateNotification($request->get('chatter'), $id, $class, $fields, $additionalInfo, $config));
        } catch (Exception $e) {
            return $this->returnAction(false, $e->getMessage());
        }

        return $this->returnAction(true, '');
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

    public function returnAction(bool $success, string $msg): Response
    {
        return $this->json(
            [
                'success' => $success,
                'msg' => $msg,
            ],
            $success ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
