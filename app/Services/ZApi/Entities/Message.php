<?php

namespace App\Services\ZApi\Entities;

/**
 * Class Message
 *
 * Represents a single message entity.
 */
class Message
{
    public string $zaap_id;

    public string $message_id;

    /**
     * Message constructor.
     */
    public function __construct(array $data)
    {
        $this->zaap_id = data_get($data, 'zaapId');
        $this->message_id = data_get($data, 'messageId');
    }
}
