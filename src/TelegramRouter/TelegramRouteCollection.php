<?php
declare(strict_types=1);

namespace App\TelegramRouter;

class TelegramRouteCollection
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @param TelegramRoute $route
     * @return TelegramRoute
     */
    public function add(TelegramRoute $route): TelegramRoute
    {
        $this->collection[$route->getUpdateType()][] = $route;
        return $route;
    }

    /**
     * @param string $updateType
     * @return TelegramRoute[]
     */
    public function get(string $updateType): ?array
    {
        return $this->collection[$updateType] ?? null;
    }
}