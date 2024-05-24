<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use App\Enums\ClientTypeEnum;
use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $clients = Client::all();
        $countAll = $clients->count();

        $countIndividual = $clients->where('type', ClientTypeEnum::INDIVIDUAL)->count();

        $countCompany = $clients->where('type', ClientTypeEnum::COMPANY)->count();

        return [
            Stat::make('Total Clientes', $countAll)
                ->color('success'),
            Stat::make('Pessoa Física', $countIndividual)
                ->color('success'),
            Stat::make('Pessoa Juridica', $countCompany)
                ->color('warning'),
        ];
    }
}
