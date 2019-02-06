<?php
declare(strict_types=1);

namespace App\TelegramController;


use App\Service\ImageMaker;
use App\Service\TelegramDownloader;
use App\TelegramMethod\SendMessageMethod;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TgBotApi\BotApiBase\BotApiInterface;
use TgBotApi\BotApiBase\Method\GetFileMethod;
use TgBotApi\BotApiBase\Method\SendChatActionMethod;
use TgBotApi\BotApiBase\Method\SendPhotoMethod;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiBase\Type\InputFileType;
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
     * @throws \TgBotApi\BotApiBase\Exception\BadArgumentException
     */
    public function __invoke(ChatType $chat, UserType $user, PhotoSizeType $photo)
    {
        $this->bot->sendChatAction(SendChatActionMethod::create($chat->id, SendChatActionMethod::ACTION_UPLOAD_PHOTO));
        $path = sprintf('%s/%s', $this->params->get('photo_temp_dir'), $photo->fileId);
        $file = $this->bot->getFile(GetFileMethod::create($photo->fileId));
        $file = $this->downloader->download($this->bot->getAbsoluteFilePath($file), $path);
        $extractor = new ColorExtractor(Palette::fromFilename($file->getPathname()));
        $palette = $extractor->extract(10);
        $colors = [];
        foreach ($palette as $color) {
            $colors[] = Color::fromIntToHex($color);
        }
        $generatedImage = $this->imageMaker->make($file, $colors);
        $this->bot->send(SendPhotoMethod::create(
            $chat->id,
            InputFileType::create($generatedImage->getPathname()),
            ['caption' => implode("\n", $colors)]
        ));
        unlink($file->getPathname());
        unlink($generatedImage->getPathname());
    }
}
