<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use LemonMind\MessageBundle\Services\MessageService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function indexAction(Request $request, int $id, ContainerInterface $container): Response
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

        $success = MessageService::create($request->get('chatter'), $product, $class, $fields, $additionalInfo, $config, $this->chatter, $this->texter);

        return $this->returnAction($success);
    }

    /**
     * @Route("/class")
     */
    public function classAction(ContainerInterface $container): Response
    {
        $classes = [];
        $config = $container->getParameter('lemonmind_message');

        foreach ($config as $key => $value) {
            if ($key === 'allowed_chatters') {
                continue;
            }
            $classes[] = $key;
        }

        return $this->json(
            [
                'classes' => $classes,
                'allowed_chatters' => $config['allowed_chatters']
            ],
            Response::HTTP_OK
        );
    }

    public function returnAction(bool $success): Response
    {
        if ($success) {
            return $this->json(
                [
                    'success' => $success,
                ],
                Response::HTTP_OK
            );
        }

        return $this->json(
            [
                'success' => $success,
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
