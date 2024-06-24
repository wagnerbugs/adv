<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionTypeEnum: int implements HasColor, HasLabel
{
    case INCOME = 1;
    case EXPENSE = 2;
    case TRANSFER = 3;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INCOME => 'Receita',
            self::EXPENSE => 'Despesa',
            self::TRANSFER => 'Transferência',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INCOME => 'success',
            self::EXPENSE => 'danger',
            self::TRANSFER => 'warning',
        };
    }
}
