<?php
declare(strict_types=1);

namespace App\TelegramRouter;


use TgBotApi\BotApiBase\Method\Interfaces\MethodInterface;

interface TelegramResponseInterface
{
    public function getTelegramRequest(): MethodInterface;

    public function getResponseType(): ?string;

    public function getCallback(): ?callable;
}