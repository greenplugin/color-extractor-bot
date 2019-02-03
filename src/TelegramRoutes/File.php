<?php
declare(strict_types=1);

namespace App\TelegramRoutes;


use App\TelegramController\TelegramClarionMessageController;
use App\TelegramRouter\RouterUpdateInterface;
use App\TelegramRouter\Rules\DocumentFileNameRule;
use App\TelegramRouter\Rules\DocumentSizeRule;
use App\TelegramRouter\TelegramRoute;
use App\TelegramRouter\TelegramRouteCollection;

class File implements RouteSetterInterface
{

    public function register(TelegramRouteCollection $collection): void
    {

    }
}