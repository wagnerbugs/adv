<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentTypeEnum: int implements HasColor, HasLabel
{
    case CPF = 1;
    case RG = 2;
    case CNH = 3;
    case OAB = 4;
    case CTPS = 5;
    case PIS = 6;
    case VOTER_ID = 7;
    case PASSPORT = 8;
    case MILITARY_ID = 9;
    case OTHER = 99;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CPF => 'CPF',
            self::RG => 'RG',
            self::CNH => 'CNH',
            self::OAB => 'OAB',
            self::CTPS => 'CTPS',
            self::PIS => 'PIS',
            self::VOTER_ID => 'Título de Eleitor',
            self::PASSPORT => 'Passaporte',
            self::MILITARY_ID => 'Caterira de Reservista',
            self::OTHER => 'Outro',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CPF => 'primary',
            self::RG => 'primary',
            self::CNH => 'primary',
            self::OAB => 'danger',
            self::CTPS => 'primary',
            self::PIS => 'primary',
            self::VOTER_ID => 'primary',
            self::PASSPORT => 'primary',
            self::MILITARY_ID => 'primary',
            self::OTHER => 'success',
        };
    }
}
