<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmploymentTypeEnum: int implements HasColor, HasLabel
{
    case CLT = 1;
    case PJ = 2;
    case INTERNSHIP = 3;
    case THIRD_PARTY = 4;
    case FIXED_TERM_CONTRACT = 5;
    case INTERMITTENT_WORK = 6;
    case HOME_OFFICE = 7;
    case YOUNG_APPRENTICE = 8;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CLT => 'Contratação CLT',
            self::PJ => 'Contratação PJ',
            self::INTERNSHIP => 'Estágio',
            self::THIRD_PARTY => 'Terceirizado',
            self::FIXED_TERM_CONTRACT => 'Contrato por Tempo Determinado',
            self::INTERMITTENT_WORK => 'Trabalho Intermitente',
            self::HOME_OFFICE => 'Home Office',
            self::YOUNG_APPRENTICE => 'Jovem Aprendiz',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CLT => 'success',
            self::PJ => 'info',
            self::INTERNSHIP => 'success',
            self::THIRD_PARTY => 'success',
            self::FIXED_TERM_CONTRACT => 'success',
            self::INTERMITTENT_WORK => 'success',
            self::HOME_OFFICE => 'success',
            self::YOUNG_APPRENTICE => 'success',
        };
    }
}
