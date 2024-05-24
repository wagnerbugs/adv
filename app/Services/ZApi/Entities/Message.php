<?php

namespace App\Services\ZApi\Entities;

class Message
{
    public string $text;

    public function __construct(array $data)
    {
        $this->text = data_get($data, 'text');
    }
}
