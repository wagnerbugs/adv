<?php

namespace App\Filament\Resources\ClientResource\Pages;

use Filament\Actions;
use App\Enums\ClientTypeEnum;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\Widgets\StatsOverview;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->label('Todos'),

            'individuals' => Tab::make()
                ->label('Pessoa Física')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ClientTypeEnum::INDIVIDUAL)),

            'companies' => Tab::make()
                ->label('Pessoa Jurídica')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ClientTypeEnum::COMPANY)),
        ];
    }
}
