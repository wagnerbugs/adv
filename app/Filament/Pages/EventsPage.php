<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EventsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.events-page';

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $navigationGroup = 'WORKFLOW';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Agenda';

    protected ?string $subheading = 'Contém todos os eventos do escritório';

    public static function canAccess(): bool
    {
        return  auth()->user()->hasPermissionTo('app_events');
    }
}
