<?php

namespace App\Traits;

trait CapitalizeTrait
{
    public function capitalize(?string $string): string
    {
        if (is_null($string) || $string === '') {
            return '';
        }

        $exceptions = ['e', 'da', 'do', 'de', 'das', 'dos'];
        $string = strtolower($string);
        $string = ucwords($string);
        $words = explode(' ', $string);

        foreach ($words as &$word) {
            if (in_array(strtolower($word), $exceptions)) {
                $word = strtolower($word);
            }
        }

        return implode(' ', $words);
    }
}
