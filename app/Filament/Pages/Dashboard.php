<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardMovimentWidget;
use App\Filament\Widgets\EventsWidget;
use Filament\Facades\Filament;
use App\Filament\Widgets\LexifyWidget;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = 0;


    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets([
            DashboardMovimentWidget::class,

        ]);
    }


    public function getColumns(): int | string | array
    {
        return 3;
    }
}
