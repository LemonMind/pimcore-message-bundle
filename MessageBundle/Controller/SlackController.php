<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle\Controller;

use LemonMind\MessageBundle\Model\SlackMessageModel;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Car;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SlackController extends FrontendController
{
    /**
     * @Route("/lemonmind_message/{id}", requirements={"id"="\d+"}))
     */
    public function indexAction(int $id, ChatterInterface $chatter, ContainerInterface $container)
    {
        $product = $container->getParameter('class_to_send')::getById($id);
        $fields = explode(',', $container->getParameter('fields_to_send'));

        $slack = new SlackMessageModel($product, $fields);
        try {
            $chatter->send($slack->create());
        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage(), $e->getCode());
        }

        return $this->redirect('https://app.slack.com/client');
        // return $this->render('index.html.twig', ['car' => $product]);
    }
}
