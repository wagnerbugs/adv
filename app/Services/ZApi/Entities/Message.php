<?php

namespace App\Services\ZApi\Entities;

/**
 * Class Message
 *
 * Represents a single message entity.
 */
class Message
{
    /**
     * @var string
     */
    public string $zaap_id;

    /**
     * @var string
     */
    public string $message_id;

    /**
     * Message constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->zaap_id = data_get($data, 'zaapId');
        $this->message_id = data_get($data, 'messageId');
    }
}
