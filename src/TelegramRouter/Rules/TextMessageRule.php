<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;


use App\TelegramRouter\RouterUpdateInterface;

class TextMessageRule implements RouteRuleInterface
{

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        return (bool)$update->getUpdate()->message->text;
    }
}