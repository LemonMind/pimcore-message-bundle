<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use LemonMind\MessageBundle\Model\SlackMessageModel;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;

/**
 * @Route("/admin/slack")
 */
class SlackController extends AdminController
{
    /**
     * @Route("/send-notification/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(int $id, ChatterInterface $chatter, ContainerInterface $container): Response
    {
        \Pimcore::unsetAdminMode();

        $class = $container->getParameter('class_to_send');

        if (class_exists($class)) {
            $product = $class::getById($id);
        } else {
            return $this->json(
                [
                    'success' => false,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $product::setGetInheritedValues(true);

        $fields = explode(',', $container->getParameter('fields_to_send'));
        $slack = new SlackMessageModel($product, $fields);

        try {
            $chatter->send($slack->create());
        } catch (TransportExceptionInterface $e) {
            return $this->json(
                [
                    'success' => false,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json([
            'success' => true,
        ], Response::HTTP_OK);
    }
}
