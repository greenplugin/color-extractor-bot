<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TelegramMessageLog;
use App\TelegramRouter\Context;
use App\TelegramRouter\RouterUpdateInterface;
use App\TelegramRouter\RouterUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use TgBotApi\BotApiBase\BotApiInterface;
use TgBotApi\BotApiBase\Method\SendChatActionMethod;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiBase\WebhookFetcherInterface;

class TelegramUpdateService
{

    /**
     * @var WebhookFetcherInterface
     */
    private $fetcher;

    private $bot;

    private $router;

    /**
     * TelegramUpdateService constructor.
     *
     * @param WebhookFetcherInterface $fetcher
     * @param TelegramRouter          $router
     * @param BotApiInterface         $bot
     */
    public function __construct(
        WebhookFetcherInterface $fetcher,
        TelegramRouter $router,
        BotApiInterface $bot
    ) {
        $this->fetcher = $fetcher;
        $this->router = $router;
        $this->bot = $bot;
    }

    /**
     * @param string $input
     * @throws \App\TelegramRouter\Exceptions\RouterParameterException
     * @throws \ReflectionException
     * @throws \TgBotApi\BotApiBase\Exception\BadRequestException
     * @throws \TgBotApi\BotApiBase\Exception\ResponseException
     * @throws \App\TelegramRouter\Exceptions\RouteExtractionException
     */
    public function acceptRequest(string $input): void
    {
        $this->acceptUpdate($this->fetcher->fetch($input));
    }

    /**
     * @param UpdateType $update
     * @throws \App\TelegramRouter\Exceptions\RouterParameterException
     * @throws \ReflectionException
     * @throws \TgBotApi\BotApiBase\Exception\ResponseException
     * @throws \App\TelegramRouter\Exceptions\RouteExtractionException
     */
    public function acceptUpdate(UpdateType $update): void
    {
        $routerUpdate = new RouterUpdateType($update, new Context());

        $response = $this->router->dispatch($routerUpdate);

        if ($response) {
            $telegramResponse = $this->bot->call($response->getTelegramRequest(), $response->getResponseType());
            if ($response->getCallback()) {
                $response->getCallback()($telegramResponse);
            }
        }
    }
}
