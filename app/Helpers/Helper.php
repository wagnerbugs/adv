<?php

namespace App\Helpers;

class Helper
{
    /**
     * Cleans the input text by removing HTML tags, decoding HTML entities,
     * removing control characters, replacing multiple spaces with a single space,
     * and trimming leading and trailing spaces.
     *
     * @param  string  $input  The input text to be cleaned.
     * @return string The cleaned text.
     */
    public static function cleanText($input)
    {
        if (! is_string($input) || empty($input)) {
            return '';
        }
        // Remover tags HTML
        $cleaned = strip_tags($input);

        // Substituir entidades HTML por seus caracteres correspondentes
        $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remover caracteres de controle como \n e \t
        $cleaned = preg_replace('/[\r\n\t]/', ' ', $cleaned);

        // Remover múltiplos espaços em branco
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        // Trim espaços no início e no final da string
        $cleaned = trim($cleaned);

        return $cleaned;
    }
}
