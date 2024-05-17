<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentTypeEnum: int implements HasLabel, HasColor
{
    case CPF = 1;
    case RG = 2;
    case OAB = 3;
    case CNH = 4;
    case PASSPORT = 5;
    case WORK_PERMIT = 6;
    case MILITARY_ID = 7;
    case OTHER = 0;

    public static function getValues(): array
    {
        return array_values(self::cases());
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CPF => 'CPF',
            self::RG => 'RG',
            self::OAB => 'OAB',
            self::CNH => 'CNH',
            self::PASSPORT => 'Passaporte',
            self::WORK_PERMIT => 'Carteira de trabalho',
            self::MILITARY_ID => 'Caterira de Reservista',
            self::OTHER => 'Outro',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CPF => 'primary',
            self::RG => 'primary',
            self::OAB => 'primary',
            self::CNH => 'primary',
            self::PASSPORT => 'primary',
            self::WORK_PERMIT => 'primary',
            self::MILITARY_ID => 'primary',
            self::OTHER => 'primary',
        };
    }
}
