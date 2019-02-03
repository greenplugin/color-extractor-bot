<?php
declare(strict_types=1);

namespace App\TelegramRouter\Extractor;


use App\TelegramRouter\Exceptions\RouteExtractionException;
use App\TelegramRouter\RouterUpdateInterface;

class PhotoExtractor extends AbstractExtractor
{
    /**
     * this extractor is example, and it can not usable in project
     *
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, array $fields): void
    {
        foreach ($fields as $key => $field) {
            $this->checkContextAvailablity($update->getContext(), $key);
            if (is_callable($field)) {
                $photo = $field($update->getUpdate()->message->photo);
                if (!$photo) {
                    throw new RouteExtractionException('Lambda function must be return value');
                }
                $update->getContext()->set($key, $photo);
                continue;
            }
            if ($field === 'all') {
                $update->getContext()->set($key, $update->getUpdate()->message->photo);
                continue;
            }

            $photos = $update->getUpdate()->message->photo;
            $otherTypes = ['min' => count($photos) - 1, 'max' => 0];

            if (isset($otherTypes[$field])) {
                $update->getContext()->set($key, $photos[$otherTypes[$field]]);
                continue;
            }
            throw new RouteExtractionException('Unsupported mode, please use "all", "max", "min", or lambda function as field value');
        }
    }

    /**
     * @param RouterUpdateInterface $update
     */
    public function extractBeforeMatch(RouterUpdateInterface $update): void
    {
        // TODO: Implement extractBeforeMatch() method.
    }
}
