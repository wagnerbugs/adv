<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EducationLevelEnum: int implements HasColor, HasLabel
{
    case NONE = 1;
    case ELEMENTARY_INCOMPLETE = 2;
    case ELEMENTARY_COMPLETE = 3;
    case HIGH_SCHOOL_INCOMPLETE = 4;
    case HIGH_SCHOOL_COMPLETE = 5;
    case TECHNICAL = 6;
    case GRADUATE_INCOMPLETE = 7;
    case GRADUATE_COMPLETE = 8;
    case POSTGRADUATE = 9;
    case MASTER = 10;
    case DOCTORATE = 11;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::NONE => 'Analfabeto',
            self::ELEMENTARY_INCOMPLETE => 'Fundamental incompleto',
            self::ELEMENTARY_COMPLETE => 'Fundamental completo',
            self::HIGH_SCHOOL_INCOMPLETE => 'Ensino médio incompleto',
            self::HIGH_SCHOOL_COMPLETE => 'Ensino médio completo',
            self::TECHNICAL => 'Técnico',
            self::GRADUATE_INCOMPLETE => 'Graduação incompleta',
            self::GRADUATE_COMPLETE => 'Graduação completa',
            self::POSTGRADUATE => 'Pós-graduação',
            self::MASTER => 'Mestrado',
            self::DOCTORATE => 'Doutorado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NONE => 'danger',
            self::ELEMENTARY_INCOMPLETE => 'danger',
            self::ELEMENTARY_COMPLETE => 'danger',
            self::HIGH_SCHOOL_INCOMPLETE => 'danger',
            self::HIGH_SCHOOL_COMPLETE => 'danger',
            self::TECHNICAL => 'warning',
            self::GRADUATE_INCOMPLETE => 'warning',
            self::GRADUATE_COMPLETE => 'warning',
            self::POSTGRADUATE => 'success',
            self::MASTER => 'success',
            self::DOCTORATE => 'success',
        };
    }
}
