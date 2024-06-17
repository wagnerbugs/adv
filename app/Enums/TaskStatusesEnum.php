<?php

namespace App\Enums;

use Illuminate\Support\Collection;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatusesEnum: string implements HasLabel, HasColor, HasIcon
{
    use IsKanbanStatus;

    case TODO = 'todo';
    case DOING = 'doing';
    case DONE = 'done';

    public static function statuses(): Collection
    {
        return collect(static::kanbanCases())
            ->map(function (self $item) {
                return [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                ];
            });
    }

    public static function kanbanCases(): array
    {
        return static::cases();
    }

    public function getId(): string
    {
        return $this->value;
    }

    public function getTitle(): string
    {
        return match ($this) {
            self::TODO => 'A fazer',
            self::DOING => 'Fazendo',
            self::DONE => 'Feito',
        };
    }


    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TODO => 'A fazer',
            self::DOING => 'Fazendo',
            self::DONE => 'Feito',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TODO => 'danger',
            self::DOING => 'warnig',
            self::DONE => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TODO => 'heroicon-s-clock',
            self::DOING => 'heroicon-s-briefcase',
            self::DONE => 'heroicon-s-check',
        };
    }
}
