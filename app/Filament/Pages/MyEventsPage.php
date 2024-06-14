<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MyEventsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.my-events-page';

    protected static ?string $navigationLabel = 'Minha Agenda';

    protected static ?string $navigationGroup = 'WORKFLOW';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationParentItem = 'Agenda';

    protected static ?string $title = 'Minha Agenda';

    protected ?string $subheading = 'Contém todos os eventos que criei ou em que estou participando.';

    public static function canAccess(): bool
    {
        return  auth()->user()->hasPermissionTo('app_my_events');
    }
}
