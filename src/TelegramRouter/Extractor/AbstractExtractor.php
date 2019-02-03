<?php
declare(strict_types=1);

namespace App\TelegramRouter\Extractor;


use App\TelegramRouter\Context;
use App\TelegramRouter\Exceptions\RouteExtractionException;
use App\TelegramRouter\RouterUpdateInterface;

abstract class AbstractExtractor implements ExtractorInterface
{

    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     */
    abstract public function extract(RouterUpdateInterface $update, array $fields): void;

    /**
     * @param RouterUpdateInterface $update
     */
    abstract public function extractBeforeMatch(RouterUpdateInterface $update): void;

    protected function checkContextAvailablity(Context $context, string $key): void
    {
        if ($context->isSet($key)) {
            throw new RouteExtractionException(sprintf('%s variable already set in context', $key));
        }
    }
}
