<?php
declare(strict_types=1);

namespace App\TelegramController;


use App\TelegramRouter\RouterUpdateInterface;
use TgBotApi\BotApiBase\Type\PhotoSizeType;

class TelegramImagePaletteController
{
    public function __invoke(RouterUpdateInterface $update, $photo)
    {
        dump($photo);
    }
}
