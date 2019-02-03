<?php
declare(strict_types=1);

namespace App\TelegramRouter;


use TgBotApi\BotApiBase\Method\Interfaces\MethodInterface;

class TelegramResponse implements TelegramResponseInterface
{

    private $method;
    private $responseType;
    private $callback;

    public function __construct(MethodInterface $method, ?string $responseType = null, callable $callback = null)
    {
        $this->method = $method;
        $this->responseType = $responseType;
        $this->callback = $callback;
    }

    public function getTelegramRequest(): MethodInterface
    {
        return $this->method;
    }

    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    public function then(callable $callback): TelegramResponse
    {
        $this->callback = $callback;
        return $this;
    }
}