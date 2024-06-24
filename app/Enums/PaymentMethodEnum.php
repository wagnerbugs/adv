<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethodEnum: int implements HasColor, HasLabel, HasIcon
{
    case MONEY = 1;
    case PIX = 2;
    case CREDIT_CARD = 3;
    case DEBIT_CARD = 4;
    case BOLETO = 5;
    case DEPOSIT = 6;
    case TRANSFER = 7;
    case OTHER = 0;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MONEY => 'Dinheiro',
            self::PIX => 'Pix',
            self::CREDIT_CARD => 'Cartão de Crédito',
            self::DEBIT_CARD => 'Cartão de Débito',
            self::BOLETO => 'Boleto',
            self::DEPOSIT => 'Depósito',
            self::TRANSFER => 'Transferência',
            self::OTHER => 'Outro',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MONEY => 'success',
            self::PIX => 'success',
            self::CREDIT_CARD => 'success',
            self::DEBIT_CARD => 'success',
            self::BOLETO => 'success',
            self::DEPOSIT => 'success',
            self::TRANSFER => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::MONEY => 'heroicon-o-banknotes',
            self::PIX => 'heroicon-o-currency-dollar',
            self::CREDIT_CARD => 'heroicon-o-credit-card',
            self::DEBIT_CARD => 'heroicon-o-credit-card',
            self::BOLETO => 'heroicon-o-document-text',
            self::DEPOSIT => 'heroicon-o-currency-dollar',
            self::TRANSFER => 'heroicon-o-currency-dollar',
        };
    }
}
