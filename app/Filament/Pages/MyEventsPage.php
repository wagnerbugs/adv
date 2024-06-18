<?php

namespace App\Filament\Pages;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class MyEventsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.my-events-page';

    protected static ?string $navigationLabel = 'Minha Agenda';

    protected static ?string $navigationGroup = 'WORKFLOW';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationParentItem = 'Agenda';

    protected static ?string $title = 'Minha Agenda';

    protected ?string $subheading = 'Contém todos os eventos que criei ou em que estou participando.';

    public $events;

    public function mount(): void
    {
        $startOfToday = Carbon::now()->startOfDay();
        $endOfMonth = Carbon::now()->endOfMonth();

        $this->events = Event::with('tags')
            ->where(function ($query) {
                $query->whereJsonContains('professionals', auth()->user()->id)
                    ->orWhere('user_id', auth()->user()->id);
            })
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
            ->where(function ($query) {
                $query->where('user_id', auth()->user()->id)
                    ->orWhere(function ($query) {
                        $query->whereJsonContains('professionals', auth()->user()->id);
                    });
            })
            ->where('starts_at', '>=', $startOfToday)
            ->where('ends_at', '<=', $endOfMonth)
            ->orderBy('starts_at', 'asc')
            ->get();
    }

    public static function canAccess(): bool
    {
        // return auth()->user()->hasPermissionTo('app_my_events');
        return true;
    }
}
