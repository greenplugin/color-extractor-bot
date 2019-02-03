<?php
declare(strict_types=1);

namespace App\TelegramRoutes;


use App\TelegramRouter\TelegramRouteCollection;

interface RouteSetterInterface
{
    public function register(TelegramRouteCollection $collection): void;
}