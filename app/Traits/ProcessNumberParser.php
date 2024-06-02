<?php

namespace App\Traits;

trait ProcessNumberParser
{
    public function processNumberParser(string $processNumber): array
    {
        $processNumber = preg_replace('/[^0-9]/', '', $processNumber);

        return [
            'process_number' => substr($processNumber, 0, 7),
            'process_digit' => substr($processNumber, 7, 2),
            'process_year' => substr($processNumber, 9, 4),
            'court_code' => substr($processNumber, 13, 1),
            'court_state_code' => substr($processNumber, 14, 2),
            'court_disctric_code' => substr($processNumber, 16, 4),
        ];
    }
}
