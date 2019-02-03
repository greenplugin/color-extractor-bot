<?php
declare(strict_types=1);

namespace App\TelegramRouter;

use TgBotApi\BotApiBase\Type\UpdateType;

/**
 * Class RouterUpdateType
 *
 * @package App\TelegramRouter
 */
class RouterUpdateType implements RouterUpdateInterface
{
    /**
     * @var UpdateType
     */
    private $update;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var TelegramRoute
     */
    private $route;

    /**
     * RouterUpdateType constructor.
     *
     * @param UpdateType $update
     * @param Context    $context
     */
    public function __construct(UpdateType $update, Context $context)
    {
        $this->update = $update;
        $this->context = $context;
    }

    /**
     * @return UpdateType
     */
    public function getUpdate(): UpdateType
    {
        return $this->update;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        foreach (static::UPDATE_TYPES as $type) {
            if ($this->update->$type) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @return TelegramRoute
     */
    public function getRoute(): TelegramRoute
    {
        return $this->route;
    }

    /**
     * @param TelegramRoute $route
     */
    public function setRoute(TelegramRoute $route): void
    {
        $this->route = $route;
    }
}