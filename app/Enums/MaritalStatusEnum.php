<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaritalStatusEnum: int implements HasColor, HasLabel
{
    case SINGLE = 1;
    case MARRIED = 2;
    case SEPARATED = 3;
    case DIVORCED = 4;
    case WIDOWED = 5;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SINGLE => 'Solteiro(a)',
            self::MARRIED => 'Casado(a)',
            self::SEPARATED => 'Separado(a)',
            self::DIVORCED => 'Divorciado(a)',
            self::WIDOWED => 'Viuvo(a)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SINGLE => 'secondary',
            self::MARRIED => 'success',
            self::SEPARATED => 'danger',
            self::DIVORCED => 'warning',
            self::WIDOWED => 'danger',
        };
    }
}
