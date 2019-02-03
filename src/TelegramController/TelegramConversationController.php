<?php
declare(strict_types=1);

namespace App\TelegramController;


use App\TelegramMethod\SendMessageMethod;
use App\TelegramRouter\RouterUpdateInterface;
use App\TelegramRouter\TelegramResponse;

/**
 * Class TelegramConversationController
 *
 * @package App\TelegramController
 */
class TelegramConversationController
{
    /**
     * @param RouterUpdateInterface $update
     * @return TelegramResponse
     */
    public function replyHi(RouterUpdateInterface $update): TelegramResponse
    {
        $messageText = sprintf('hi, %s!', $update->getUpdate()->message->from->firstName);
        return new TelegramResponse(
            SendMessageMethod::make($update->getUpdate()->message->chat, $messageText)
                ->setReplyToUpdate($update->getUpdate())
        );
    }
}
