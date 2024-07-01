<?php

namespace App\Filament\Widgets;

use App\Models\Movement;
use Filament\Widgets\Widget;

class DashboardMovimentWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-moviment-widget';

    protected static ?int $sort = 1;

    public $movements = [];

    public function mount(): void
    {
        $this->movements = Movement::orderBy('event_date', 'desc')->limit(30)->get();
    }
}
