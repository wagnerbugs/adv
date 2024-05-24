<?php

namespace App\Services\ZApi\Entities;

/**
 * Class Message
 *
 * Represents a single message entity.
 */
class Message
{
    public string $text;

    /**
     * Message constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->text = data_get($data, 'text');
    }
}
