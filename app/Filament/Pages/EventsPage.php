<?php

namespace App\Filament\Pages;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class EventsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.events-page';

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $navigationGroup = 'WORKFLOW';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Agenda';

    protected ?string $subheading = 'Contém todos os eventos do escritório';

    public $events;

    public function mount(): void
    {
        $startOfToday = Carbon::now()->startOfDay();
        $endOfMonth = Carbon::now()->endOfMonth();

        $this->events = Event::with('tags')
            ->where('is_private', false)
            ->where('starts_at', '>=', $startOfToday)
            ->where('ends_at', '<=', $endOfMonth)
            ->orderBy('starts_at', 'asc')
            ->get();
    }

    #[On('event-changed')]
    public function refreshEvents()
    {
        $startOfToday = Carbon::now()->startOfDay();
        $endOfMonth = Carbon::now()->endOfMonth();

        $this->events = Event::with('tags')
            ->where('is_private', false)
            ->where('starts_at', '>=', $startOfToday)
            ->where('ends_at', '<=', $endOfMonth)
            ->orderBy('starts_at', 'asc')
            ->get();
    }

    public static function canAccess(): bool
    {
        // return  auth()->user()->hasPermissionTo('app_events');
        return true;
    }
}
