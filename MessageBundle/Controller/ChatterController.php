<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use LemonMind\MessageBundle\Model\SlackMessageModel;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;

/**
 * @Route("/admin/chatter")
 */
class ChatterController extends AdminController
{
    private bool $success = true;

    /**
     * @Route("/send-notification/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(Request $request, int $id, ChatterInterface $chatter, ContainerInterface $container): Response
    {
        \Pimcore::unsetAdminMode();

        $class = $container->getParameter('class_to_send');
        $product = $class::getById($id);

        if ($product instanceof AbstractObject) {
            $product::setGetInheritedValues(true);

            $fields = explode(',', $container->getParameter('fields_to_send'));
            $additionalInfo = $request->get('additionalInfo');

            switch ($request->get('chatter')) {
                case 'googlechat':
                    $this->googlechat($product, $fields, $chatter, $additionalInfo);
                    break;
                case 'slack':
                    $this->slack($product, $fields, $chatter, $additionalInfo);
                    break;
                case 'chattersAll':
                    $this->slack($product, $fields, $chatter, $additionalInfo);
                    $this->googlechat($product, $fields, $chatter, $additionalInfo);
                    break;
                case 'email':
                    $this->email($product, $fields, $chatter, $additionalInfo);
                    break;
                case 'all':
                    $this->slack($product, $fields, $chatter, $additionalInfo);
                    $this->googlechat($product, $fields, $chatter, $additionalInfo);
                    $this->email($product, $fields, $chatter, $additionalInfo);
                    break;
                default:
                    $this->success = false;
            }

            if ($this->success) {
                return $this->json(
                    [
                        'success' => $this->success,
                    ],
                    Response::HTTP_OK);
            } else {
                return $this->json(
                    [
                        'success' => $this->success,
                    ],
                    Response::HTTP_BAD_REQUEST);
            }

        } else {
            $this->success = false;
            return $this->json(
                [
                    'success' => $this->success,
                ],
                Response::HTTP_BAD_REQUEST);
        }
    }

    public function slack(object $product, array $fields, ChatterInterface $chatter, string $additionalInfo): void
    {
        $slack = new SlackMessageModel($product, $fields, $additionalInfo);
        try {
            $chatter->send($slack->create());
        } catch (TransportExceptionInterface $e) {
            $this->success = false;
        }
    }

    public function googlechat(object $product, array $fields, ChatterInterface $chatter, string $additionalInfo): void
    {
        $this->success = false;
    }

    public function email(object $product, array $fields, ChatterInterface $chatter, string $additionalInfo): void
    {
        $this->success = false;
    }
}
