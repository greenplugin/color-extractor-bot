<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use App\TelegramRouter\RouterUpdateInterface;

interface RouteRuleInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool;
}