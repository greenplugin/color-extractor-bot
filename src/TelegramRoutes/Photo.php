<?php
declare(strict_types=1);

namespace App\TelegramRoutes;


use App\TelegramController\TelegramImagePaletteController;
use App\TelegramRouter\Extractor\PhotoExtractor;
use App\TelegramRouter\RouterUpdateInterface;
use App\TelegramRouter\Rules\PhotoRule;
use App\TelegramRouter\TelegramRoute;
use App\TelegramRouter\TelegramRouteCollection;

class Photo implements RouteSetterInterface
{
    /**
     * @param TelegramRouteCollection $collection
     * @throws \App\TelegramRouter\Exceptions\RouteExtractionException
     */
    public function register(TelegramRouteCollection $collection): void
    {
        $collection->add(
            new TelegramRoute(
                RouterUpdateInterface::TYPE_MESSAGE,
                [new PhotoRule()],
                TelegramImagePaletteController::class
            )
        )->extract(['photo' => 'min'], PhotoExtractor::class);
    }
}
