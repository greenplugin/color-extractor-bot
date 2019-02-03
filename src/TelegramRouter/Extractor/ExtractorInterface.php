<?php
declare(strict_types=1);

namespace App\TelegramRouter\Extractor;


use App\TelegramRouter\RouterUpdateInterface;

/**
 * Interface ExtractorInterface
 *
 * @package App\TelegramRouter\Extractor
 */
interface ExtractorInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     */
    public function extract(RouterUpdateInterface $update, array $fields): void;

    /**
     * @param RouterUpdateInterface $update
     */
    public function extractBeforeMatch(RouterUpdateInterface $update): void;
}
