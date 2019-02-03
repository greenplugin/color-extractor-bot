<?php
declare(strict_types=1);

namespace App\TelegramMethod;


use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiBase\Type\UpdateType;

/**
 * Class SendMessageMethod
 */
class SendMessageMethod extends \TgBotApi\BotApiBase\Method\SendMessageMethod
{
    /**
     * @param ChatType   $chat
     * @param string     $text
     * @param array|null $data
     * @return SendMessageMethod
     */
    public static function make(ChatType $chat, string $text, array $data = null): SendMessageMethod
    {
        $instance = new static();

        $instance->chatId = $chat->id;
        $instance->text = $text;
        if ($data) {
            $instance->fill($data);
        }

        return $instance;
    }

    /**
     * @param UpdateType $update
     * @return SendMessageMethod
     */
    public function setReplyToUpdate(UpdateType $update): SendMessageMethod
    {
        $this->replyToMessageId = $update->message->messageId;
        return $this;
    }

    /**
     * @return SendMessageMethod
     */
    public function setParseAsHtml(): SendMessageMethod
    {
        $this->parseMode = static::PARSE_MODE_HTML;
        return $this;
    }

    /**
     * @return SendMessageMethod
     */
    public function setParseAsMarkdown(): SendMessageMethod
    {
        $this->parseMode = static::PARSE_MODE_MARKDOWN;
        return $this;
    }

    /**
     * @return SendMessageMethod
     */
    public function setDisableNotification(): SendMessageMethod
    {
        $this->disableNotification = true;
        return $this;
    }

    /**
     * @return SendMessageMethod
     */
    public function setDisableWebPagePreview(): SendMessageMethod
    {
        $this->disableWebPagePreview = true;
        return $this;
    }
}