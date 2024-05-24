<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TypeOfBankAccountEnum: int implements HasLabel, HasColor
{
    case CURRENT_ACCOUNT = 1;
    case SAVINGS_ACCOUNT = 2;
    case SALARY_ACCOUNT = 3;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CURRENT_ACCOUNT => 'Conta Corrente',
            self::SAVINGS_ACCOUNT => 'Conta Poupança',
            self::SALARY_ACCOUNT => 'Conta Salário',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CURRENT_ACCOUNT => 'primary',
            self::SAVINGS_ACCOUNT => 'success',
            self::SALARY_ACCOUNT => 'warning',
        };
    }
}
