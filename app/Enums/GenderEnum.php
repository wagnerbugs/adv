<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum GenderEnum: int implements HasLabel, HasColor
{
    case MALE = 1;
    case FEMALE = 2;
    case OTHER = 3;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function parse(?string $gender): self
    {
        return match (strtolower($gender)) {
            'masculino' => self::MALE,
            'feminino' => self::FEMALE,
            default => self::OTHER,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Feminino',
            self::OTHER => 'Outro',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MALE => 'success',
            self::FEMALE => 'danger',
            self::OTHER => 'warning',
        };
    }
}
