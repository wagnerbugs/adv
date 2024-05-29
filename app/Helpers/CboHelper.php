<?php

namespace App\Helpers;

class CboHelper
{
    public static function cboCode(?string $string)
    {
        if (is_null($string)) {
            return ''; // CBO não informado
        }

        $length = strlen($string);

        if ($length <= 2) {
            return $string;
        }

        $start = substr($string, 0, $length - 2);
        $end = substr($string, -2);

        return $start . '-' . $end;
    }

    public static function convertToFamilyCodeCbo($string)
    {
        if (is_null($string)) {
            return null;
        }

        return substr($string, 0, 4);
    }
}
