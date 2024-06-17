<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TaskPriorityEnum: string implements HasLabel, HasColor, HasIcon
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOW => 'Baixo',
            self::MEDIUM => 'Média',
            self::HIGH => 'Alta',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LOW => 'primary',
            self::MEDIUM => 'warning',
            self::HIGH => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::LOW => 'heroicon-o-flag',
            self::MEDIUM => 'heroicon-o-flag',
            self::HIGH => 'heroicon-o-flag',
        };
    }
}
