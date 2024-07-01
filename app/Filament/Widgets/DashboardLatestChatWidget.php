<?php

namespace App\Filament\Widgets;

use App\Models\Movement;
use Filament\Widgets\Widget;

class DashboardLatestChatWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-latest-chat-widget';

    protected static ?int $sort = 3;

    public $movements = [];

    public function mount(): void
    {
        $this->movements = Movement::orderBy('event_date', 'desc')->limit(30)->get();
    }

    public static function canView(): bool
    {
        return  auth()->user()->hasPermissionTo('widget_latest_processes_chats');
        // return true;
    }
}
