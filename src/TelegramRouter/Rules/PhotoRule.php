<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;


use App\TelegramRouter\RouterUpdateInterface;

class PhotoRule implements RouteRuleInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        $message = $update->getUpdate()->message;
        if (!$message->photo) {
            return false;
        }
        return true;
    }
}