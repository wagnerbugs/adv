<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProspectionReactionEnum: int implements HasColor, HasLabel
{
    case WAITING = 1;
    case SCHEDULING = 2;
    case WITH_LAWYER = 3;
    case NO_INTEREST = 4;
    case NO_CONTACT = 5;
    case BUDGET_SENT = 6;
    case STAND_BY = 7;
    case TRYING = 8;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WAITING => 'Aguardando Retorno',
            self::SCHEDULING => 'Agendando',
            self::WITH_LAWYER => 'Com Advogado',
            self::NO_INTEREST => 'Sem Interesse',
            self::NO_CONTACT => 'Sem Contato',
            self::BUDGET_SENT => 'Orcamento Enviado',
            self::STAND_BY => 'Stand By',
            self::TRYING => 'Iniciando Prospecção',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::WAITING => 'danger',
            self::SCHEDULING => 'success',
            self::WITH_LAWYER => 'warning',
            self::NO_INTEREST => 'primary',
            self::NO_CONTACT => 'primary',
            self::BUDGET_SENT => 'primary',
            self::STAND_BY => 'primary',
            self::TRYING => 'primary',
        };
    }
}
