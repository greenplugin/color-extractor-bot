<?php
declare(strict_types=1);

namespace App\TelegramRoutes;


use App\TelegramController\TelegramConversationController;
use App\TelegramController\TelegramErcMessageController;
use App\TelegramController\TelegramNotPrivateMessageController;
use App\TelegramController\TelegramNotSupportedMessageController;
use App\TelegramMethod\SendMessageMethod;
use App\TelegramRouter\RouterUpdateInterface;
use App\TelegramRouter\Rules\ChatTypeMessageRule;
use App\TelegramRouter\Rules\ContentTextRule;
use App\TelegramRouter\Rules\RegexRule;
use App\TelegramRouter\Rules\TextMessageRule;
use App\TelegramRouter\TelegramResponse;
use App\TelegramRouter\TelegramRoute;
use App\TelegramRouter\TelegramRouteCollection;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiBase\Type\UpdateType;

class Message implements RouteSetterInterface
{

    /**
     * @param TelegramRouteCollection $collection
     * @throws \App\TelegramRouter\Exceptions\RouteExtractionException
     */
    public function register(TelegramRouteCollection $collection): void
    {
        $collection->add(new TelegramRoute(
            RouterUpdateInterface::TYPE_MESSAGE,
            [new ContentTextRule('hi', true)],
            TelegramConversationController::class . '::replyHi'
        ))->extract(['text' => 'message.text']);

        $collection->add(new TelegramRoute(
            RouterUpdateInterface::TYPE_MESSAGE,
            [new ContentTextRule('fuck', true)],
            function (UpdateType $update) {
                return new TelegramResponse(
                    SendMessageMethod::make($update->message->chat, 'Shit!')
                        ->setReplyToUpdate($update)
                );
            }
        ));
    }
}