<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use App\Models\User;
use App\Models\Event;
use Filament\Actions;
use App\Models\Client;
use App\Models\Process;
use App\Models\EventTag;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\EventResource;
use Illuminate\Database\Eloquent\Builder;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class MyEventsWidget extends FullCalendarWidget
{
    protected static ?int $sort = 4;

    public Model | string | null $model = Event::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->with('tags')
            ->where('user_id', auth()->user()->id)
            ->orWhere(
                function (Builder $query) {
                    $query
                        ->whereJsonContains('professionals', auth()->user()->id);
                }
            )
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Event $event) => [
                    'id' => $event->id,
                    'user_id' => $event->user_id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'color' => $event->getTagColor(),
                    'professionals' => $event->professionals,
                    'process_id' => $event->process_id,
                    'client_id' => $event->client_id,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                    'is_juridical' => $event->is_juridical,
                    'is_private' => $event->is_private,
                    'shouldOpenUrlInNewTab' => false,
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
                        ->default(auth()->user()->id)
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('color')
                        ->label('Cor')
                        ->preload()
                        ->searchable()
                        ->relationship('tags', 'title')
                        ->options(function (): array {
                            $colors = EventTag::where('is_private', false)->get();
                            $colorList = [];

                            if ($colors) {
                                foreach ($colors as $color) {
                                    $colorList[$color->id] = '<span class="flex items-center text-sm font-medium text-gray-900 dark:text-white me-3"><span class="flex w-2.5 h-2.5 rounded-full me-1.5 flex-shrink-0" style="background-color:' . $color->color . '"></span>' . $color->title . '</span>';
                                }
                            }

                            return $colorList;
                        })
                        ->createOptionForm([
                            Forms\Components\Section::make()
                                ->columns(2)
                                ->schema([

                                    Forms\Components\Hidden::make('user_id')
                                        ->default(auth()->user()->id)
                                        ->required(),

                                    Forms\Components\TextInput::make('title')
                                        ->label('Título da Tag')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\ColorPicker::make('color')
                                        ->label('Cor')
                                        ->required(),

                                    Forms\Components\Toggle::make('is_private')
                                        ->label('Uso privado?')
                                        ->default(false),
                                ])
                        ])
                        ->editOptionForm([
                            Forms\Components\Hidden::make('user_id')
                                ->required(),

                            Forms\Components\TextInput::make('title')
                                ->label('Título da Tag')
                                ->maxLength(255)
                                ->required(),

                            Forms\Components\ColorPicker::make('color')
                                ->label('Cor')
                                ->required(),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $color = EventTag::create($data);
                            return $color->id;
                        })
                        ->allowHtml(),

                    Forms\Components\Textarea::make('description')
                        ->label('Descrição')
                        ->columnSpanFull(),

                    Forms\Components\Grid::make()
                        ->columns(3)
                        ->schema([

                            Forms\Components\Select::make('professionals')
                                ->label('Responsáveis')
                                ->placeholder('Selecione o(s) responsável(eis)')
                                ->options(
                                    User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                                        ->where('user_profiles.is_active', true)
                                        ->pluck('users.name', 'users.id')
                                        ->toArray()
                                )
                                ->multiple()
                                ->preload(),

                            Forms\Components\Select::make('process_id')
                                ->label('Refere-se a um processo?')
                                ->placeholder('Selecione o processo')
                                ->searchable()
                                ->options(
                                    function (): array {
                                        $processes = Process::with('client')->get();
                                        $processesList = [];

                                        if ($processes) {
                                            foreach ($processes as $process) {

                                                $processesList[$process->id] = '<span class="text-sm font-medium me-3">' . $process->client->name . '</span><br><span class="text-gray-400 text-xs me-3">' . $process->process . '</span>';
                                            }
                                        }

                                        return $processesList;
                                    }
                                )
                                ->getSearchResultsUsing(function (string $search): array {
                                    return Process::whereHas('client', function (Builder $clientQuery) use ($search) {
                                        $clientQuery->where(function ($query) use ($search) {
                                            $query->whereHas('individual', function (Builder $individualQuery) use ($search) {
                                                $individualQuery->where('name', 'like', "%{$search}%");
                                            })
                                                ->orWhereHas('company', function (Builder $companyQuery) use ($search) {
                                                    $companyQuery->where('company', 'like', "%{$search}%");
                                                });
                                        });
                                    })
                                        ->orWhere('process', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [$item->id => "<span class='text-sm font-medium me-3'>{$item->client->name}</span><br><span class='text-gray-400 text-xs me-3'>{$item->process}</span>"];
                                        })
                                        ->toArray();
                                })
                                ->preload()
                                ->allowHtml(),

                            Forms\Components\Select::make('client_id')
                                ->label('Refere-se a um cliente?')
                                ->placeholder('Selecione o cliente')
                                ->options(
                                    function (): array {
                                        $clients = Client::all();
                                        $clientsList = [];

                                        if ($clients) {
                                            foreach ($clients as $client) {
                                                $clientsList[$client->id] = '<span class="text-sm font-medium me-3">' . $client->name . '</span><br><span class="text-gray-400 text-xs me-3">' . $client->document . '</span>';
                                            }
                                        }

                                        return $clientsList;
                                    }
                                )
                                ->getSearchResultsUsing(function (string $search): array {
                                    return Client::where('document', 'like', "%{$search}%")
                                        ->orWhere(function ($query) use ($search) {
                                            $query->whereHas('individual', function (Builder $individualQuery) use ($search) {
                                                $individualQuery->where('name', 'like', "%{$search}%");
                                            })
                                                ->orWhereHas('company', function (Builder $companyQuery) use ($search) {
                                                    $companyQuery->where('company', 'like', "%{$search}%");
                                                });
                                        })
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function ($client) {
                                            return [$client->id => "<span class='text-sm font-medium me-3'>{$client->name}</span><br><span class='text-gray-400 text-xs me-3'>{$client->document}</span>"];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->allowHtml(),

                        ]),

                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\DateTimePicker::make('starts_at')
                                ->label('Início')
                                ->seconds(false)
                                ->required(),
                            Forms\Components\DateTimePicker::make('ends_at')
                                ->label('Fim')
                                ->seconds(false)
                                ->required(),
                        ]),

                    Forms\Components\Toggle::make('is_juridical')
                        ->label('Evento Jurídico?'),

                    Forms\Components\Toggle::make('is_private')
                        ->label('Evento Pessoal?')
                        ->hintColor('warning')
                        ->hintIcon('heroicon-o-exclamation-circle')
                        ->hintIconTooltip('Só pode ser visto por você e responsáveis selecionados'),

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
                            'process_id' => $record->process_id,
                            'client_id' => $record->client_id,
                            'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                            'ends_at' => $arguments['event']['end'] ?? $record->ends_at,
                            'is_juridical' => $record->is_juridical,
                            'is_private' => $record->is_private,
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

    public static function canView(): bool
    {
        return  auth()->user()->hasPermissionTo('widget_my_events');
    }
}
