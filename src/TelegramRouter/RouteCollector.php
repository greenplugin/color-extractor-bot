<?php
declare(strict_types=1);

namespace App\TelegramRouter;


use App\TelegramRoutes\File;
use App\TelegramRoutes\Message;
use App\TelegramRoutes\Photo;
use App\TelegramRoutes\RouteSetterInterface;

class RouteCollector
{
    private $routes;
    private $collection;

    public function __construct(TelegramRouteCollection $collection)
    {
        $this->routes[] = new File();
        $this->routes[] = new Message();
        $this->routes[] = new Photo();
        $this->collection = $collection;
    }

    public function collect(): void
    {
        foreach ($this->routes as $routeSetter) {
            $this->setRoutes($routeSetter);
        }
    }

    private function setRoutes(RouteSetterInterface $setter): void
    {
        $setter->register($this->collection);
    }
}