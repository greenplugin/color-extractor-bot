<?php

namespace App\Controller;

use App\Service\TelegramUpdateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    /**
     * @Route("/webhook/{token}", name="webhook_path")
     * @param                       $token
     * @param Request               $request
     * @param ParameterBagInterface $parameterBag
     * @param TelegramUpdateService $updater
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \App\TelegramRouter\Exceptions\RouterParameterException
     * @throws \ReflectionException
     * @throws \TgBotApi\BotApiBase\Exception\BadRequestException
     * @throws \TgBotApi\BotApiBase\Exception\ResponseException
     */
    public function index($token, Request $request, ParameterBagInterface $parameterBag, TelegramUpdateService $updater)
    {
        if ($token !== $parameterBag->get('telegram.webhook_secret')) {
            throw new NotFoundHttpException();
        }

        $updater->acceptRequest($request->getContent());

        return JsonResponse::create();
    }
}
