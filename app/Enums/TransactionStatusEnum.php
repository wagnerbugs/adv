<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransactionStatusEnum: int implements HasLabel
{
    case Pending = 1;
    case Completed = 2;
    case Failed = 3;
    case Canceled = 4;
    case Refunded = 5;
    case PartiallyRefunded = 6;
    case InProcess = 7;
    case OnHold = 8;
    case Disputed = 9;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Completed => 'Completo',
            self::Failed => 'Falhou',
            self::Canceled => 'Cancelado',
            self::Refunded => 'Reembolsado',
            self::PartiallyRefunded => 'Parcialmente Reembolsado',
            self::InProcess => 'Em Processo',
            self::OnHold => 'Em Espera',
            self::Disputed => 'Disputado',
        };
    }
}
