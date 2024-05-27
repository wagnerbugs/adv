<?php

namespace App\Enums;

enum ChatbotStepsEnum: int
{
    case MENU = 1;
    case LEGAL = 2;
    case LEGAL_LIST = 21;
    case LEGAL_LIST_INFO = 22;
    case FINANCIAL = 3;
    case FINANCIAL_LIST = 31;
    case FINANCIAL_LIST_INFO = 32;
    case HUMAN_RESOURCES = 9;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
