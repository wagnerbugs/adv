<?php

namespace App\Traits;

trait ProcessNumberParser
{
    public function processNumberParser(string $processNumber): array
    {
        $processNumber = preg_replace('/[^0-9]/', '', $processNumber);

        return [
            'sequential_number' => substr($processNumber, 0, 7),
            'verification_digit' => substr($processNumber, 7, 2),
            'year' => substr($processNumber, 9, 4),
            'court' => substr($processNumber, 13, 1),
            'state_court' => substr($processNumber, 14, 2),
            'agency_code' => substr($processNumber, 16, 4),
        ];
    }
}
