<?php

namespace App\Services\CNJ\Process\Entities;

class ProcessSubject
{
    public int $subject_code;
    public string $subject_name;

    public function __construct(array $data)
    {
        $this->subject_code = data_get($data, 'codigo');
        $this->subject_name = data_get($data, 'nome');
    }
}
