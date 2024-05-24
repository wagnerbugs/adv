<?php

namespace App\Services\CnpjWs\Entities;

class CompanyError
{
    public int $status;
    public string $title;
    public string $details;

    public function __construct(array $data)
    {
        $this->status = data_get($data, 'status');
        $this->title = data_get($data, 'title');
        $this->details = data_get($data, 'details');
    }
}
