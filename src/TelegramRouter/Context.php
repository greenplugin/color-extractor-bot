<?php
declare(strict_types=1);

namespace App\TelegramRouter;


class Context
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $id
     * @return mixed|null
     */
    public function get(string $id)
    {
        return $this->data[$id] ?? null;
    }

    /**
     * @param string $id
     * @param        $value
     * @return $this
     */
    public function set(string $id, $value)
    {
        $this->data[$id] = $value;
        return $this;
    }

    public function isSet(string $id): bool
    {
        return isset($this->data[$id]);
    }
}