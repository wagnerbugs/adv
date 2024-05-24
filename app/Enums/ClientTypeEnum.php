<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClientTypeEnum: int implements HasColor, HasLabel
{
    case INDIVIDUAL = 1;
    case COMPANY = 2;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Pessoa Física',
            self::COMPANY => 'Pessoa Jurídica',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INDIVIDUAL => 'success',
            self::COMPANY => 'warning',
        };
    }
}
