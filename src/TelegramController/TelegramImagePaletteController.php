<?php
declare(strict_types=1);

namespace App\TelegramController;


use App\Service\ImageMaker;
use App\Service\TelegramDownloader;
use App\TelegramMethod\SendMessageMethod;
use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TgBotApi\BotApiBase\BotApiInterface;
use TgBotApi\BotApiBase\Method\GetFileMethod;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiBase\Type\PhotoSizeType;
use TgBotApi\BotApiBase\Type\UserType;

class TelegramImagePaletteController
{
    private $bot;
    private $downloader;
    private $params;
    private $imageMaker;

    public function __construct(
        BotApiInterface $bot,
        TelegramDownloader $downloader,
        ParameterBagInterface $params,
        ImageMaker $imageMaker
    ) {
        $this->bot = $bot;
        $this->downloader = $downloader;
        $this->params = $params;
        $this->imageMaker = $imageMaker;
    }

    /**
     * @param ChatType      $chat
     * @param UserType      $user
     * @param PhotoSizeType $photo
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \TgBotApi\BotApiBase\Exception\ResponseException
     */
    public function __invoke(ChatType $chat, UserType $user, PhotoSizeType $photo)
    {
        $path = sprintf('%s/%s', $this->params->get('photo_temp_dir'), $photo->fileId);
        $file = $this->bot->getFile(GetFileMethod::create($photo->fileId));
        $file = $this->downloader->download($this->bot->getAbsoluteFilePath($file), $path);
        $palette = Palette::fromFilename($file->getPathname());
        $colors = [];
        foreach ($palette->getMostUsedColors(10) as $color => $value) {
            $colors[] = Color::fromIntToHex($color);
        }
        $this->imageMaker->make($file, $colors);
        $this->bot->send(SendMessageMethod::make($chat, implode("\n", $colors)));
    }
}
