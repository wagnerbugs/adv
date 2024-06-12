<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use App\Models\User;
use App\Models\Event;
use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\EventResource;
use Filament\Forms\Form;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    // protected static string $view = 'filament.widgets.calendar-widget';

    protected static ?int $sort = 4;

    public Model | string | null $model = Event::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Event $event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'color' => $event->color,
                    'professionals' => $event->professionals,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                    'is_audience' => $event->is_audience,
                    // 'url' => EventResource::getUrl(name: 'edit',  parameters: ['record' => $event]),
                    'shouldOpenUrlInNewTab' => false
                ]
            )
            ->all();
    }

    public function getFormSchema(): array
    {
        return [

            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Hidden::make('user_id')
                        ->label('Usuário')
                        ->default(auth()->user()->id),

                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\ColorPicker::make('color')
                        ->label('Cor'),
                    Forms\Components\Textarea::make('description')
                        ->label('Descrição')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('professionals')
                        ->label('Profissionais')
                        ->options(
                            User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                                ->where('user_profiles.is_active', true)
                                ->pluck('users.name', 'users.id')
                                ->toArray()
                        )
                        ->multiple()
                        ->preload(),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\DateTimePicker::make('starts_at')
                                ->label('Início')
                                ->required(),
                            Forms\Components\DateTimePicker::make('ends_at')
                                ->label('Fim')
                                ->required(),
                        ]),
                    Forms\Components\Toggle::make('is_audience')
                        ->label('É audiência?')
                        ->required(),

                ]),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Adicionar Evento'),
        ];
    }

    protected function modalActions(): array
    {
        return [
            CreateAction::make()
                ->label('Adicionar Evento')
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        $form->fill([
                            'user_id' => auth()->user()->id,
                            'starts_at' => $arguments['start'] ?? null,
                            'ends_at' => $arguments['end'] ?? null,
                        ]);
                    }
                ),
            EditAction::make()
                ->mountUsing(
                    function (Event $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'id' => $record->id,
                            'user_id' => $record->user_id,
                            'title' => $record->title,
                            'description' => $record->description,
                            'color' => $record->color,
                            'professionals' => $record->professionals,
                            'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                            'ends_at' => $arguments['event']['end'] ?? $record->ends_at,
                            'is_audience' => $record->is_audience,
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make();
    }

    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }){
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
            }
        JS;
    }
}
