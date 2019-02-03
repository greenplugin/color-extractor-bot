<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;


use App\TelegramRouter\RouterUpdateInterface;

class DocumentMimeTypeRule extends DocumentRule
{

    private $mimeType;

    private $isRegex;

    public function __construct(string $mimeType, $isRegex = false)
    {
        $this->mimeType = $mimeType;
        $this->isRegex = $isRegex;
    }

    public function match(RouterUpdateInterface $update): bool
    {
        if (!parent::match($update)) {
            return false;
        }

        $document = $update->getUpdate()->message->document;

        return $this->isRegex ? mb_ereg_match($this->mimeType, $document->mimeType) : $document->mimeType === $this->mimeType;
    }
}