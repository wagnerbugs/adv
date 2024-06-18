<?php

namespace App\Filament\Pages;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusesEnum;
use App\Models\Client;
use App\Models\Process;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class TaskKanban extends KanbanBoard
{
    protected static string $model = Task::class;

    protected static ?string $navigationLabel = 'Tarefas';

    protected static ?string $navigationGroup = 'WORKFLOW';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static string $recordTitleAttribute = 'title';

    protected static string $statusEnum = TaskStatusesEnum::class;

    public bool $disableEditModal = false;

    protected bool $editModalSlideOver = true;

    protected static string $view = 'filament-kanban::kanban-board';

    protected static string $headerView = 'filament-kanban::kanban-header';

    protected static string $recordView = 'kanban.records';

    protected static string $statusView = 'filament-kanban::kanban-status';

    protected function records(): Collection
    {
        return Task::ordered()->where('is_active', true)->get();
    }

    protected function trasnformRecord(Model $record): Collection
    {
        return collect([
            'id' => $record->id,
            'title' => $record->title,
            'description' => $record->description,
            'color' => $record->color,
            'professionals' => $record->professionals,
            'process_id' => $record->process_id,
            'client_id' => $record->client_id,
            'starts_at' => $record->starts_at,
            'ends_at' => $record->ends_at,
            'priority' => $record->priority,
            'is_private' => $record->is_private,
            'is_active' => $record->is_active,
            'is_urgent' => $record->is_urgent,
            'deadline_at' => $record->created_at,
        ]);
    }

    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Task::find($recordId)->update(['status' => $status]);
        Task::setNewOrder($toOrderedIds);
    }

    public function onSortChanged(int $recordId, string $status, array $orderedIds): void
    {
        Task::setNewOrder($orderedIds);
    }

    protected function getEditModalFormSchema(?int $recordId): array
    {
        return [];
    }

    protected function editRecord($recordId, array $data, array $state): void
    {
        Task::find($recordId)->update([
            // 'phone' => $data['phone']
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('add')
                ->model(Task::class)
                ->form([
                    Section::make()
                        ->columns(2)
                        ->schema([
                            Hidden::make('user_id')
                                ->default(auth()->user()->id)
                                ->required(),

                            TextInput::make('title')
                                ->label('Título')
                                ->maxLength(255)
                                ->required(),

                            ColorPicker::make('color')
                                ->label('Cor'),

                            Textarea::make('description')
                                ->label('Descrição')
                                ->columnSpanFull(),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    Select::make('professionals')
                                        ->label('Responsáveis')
                                        ->placeholder('Selecione o(s) responsável(eis)')
                                        ->options(User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')->where('user_profiles.is_active', true)->pluck('users.name', 'users.id')->toArray())
                                        ->multiple()
                                        ->preload(),

                                    Select::make('process_id')
                                        ->label('Refere-se a um processo?')
                                        ->placeholder('Selecione o processo')
                                        ->searchable()
                                        ->options(function (): array {
                                            $processes = Process::with('client')->get();
                                            $processesList = [];

                                            if ($processes) {
                                                foreach ($processes as $process) {
                                                    $processesList[$process->id] =
                                                        '
                                                <span class="me-3 text-sm font-medium">'.
                                                        $process->client->name.
                                                        '</span><br><span class="me-3 text-xs text-gray-400">'.
                                                        $process->process.
                                                        '</span>';
                                                }
                                            }

                                            return $processesList;
                                        })
                                        ->getSearchResultsUsing(function (string $search): array {
                                            return Process::whereHas('client', function (Builder $clientQuery) use ($search) {
                                                $clientQuery->where(function ($query) use ($search) {
                                                    $query
                                                        ->whereHas('individual', function (Builder $individualQuery) use ($search) {
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
                                                    return [$item->id => "<span class='me-3 text-sm font-medium'>{$item->client->name}</span><br><span class='me-3 text-xs text-gray-400'>{$item->process}</span>"];
                                                })
                                                ->toArray();
                                        })
                                        ->preload()
                                        ->allowHtml(),

                                    Select::make('client_id')
                                        ->label('Refere-se a um cliente?')
                                        ->placeholder('Selecione o cliente')
                                        ->options(function (): array {
                                            $clients = Client::all();
                                            $clientsList = [];

                                            if ($clients) {
                                                foreach ($clients as $client) {
                                                    $clientsList[$client->id] = '<span class="me-3 text-sm font-medium">'.$client->name.'</span><br><span class="me-3 text-xs text-gray-400">'.$client->document.'</span>';
                                                }
                                            }

                                            return $clientsList;
                                        })
                                        ->getSearchResultsUsing(function (string $search): array {
                                            return Client::where('document', 'like', "%{$search}%")
                                                ->orWhere(function ($query) use ($search) {
                                                    $query
                                                        ->whereHas('individual', function (Builder $individualQuery) use ($search) {
                                                            $individualQuery->where('name', 'like', "%{$search}%");
                                                        })
                                                        ->orWhereHas('company', function (Builder $companyQuery) use ($search) {
                                                            $companyQuery->where('company', 'like', "%{$search}%");
                                                        });
                                                })
                                                ->limit(50)
                                                ->get()
                                                ->mapWithKeys(function ($client) {
                                                    return [$client->id => "<span class='me-3 text-sm font-medium'>{$client->name}</span><br><span class='me-3 text-xs text-gray-400'>{$client->document}</span>"];
                                                })
                                                ->toArray();
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->allowHtml(),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    DateTimePicker::make('starts_at')
                                        ->label('Início')
                                        ->seconds(false)
                                        ->required(),
                                    DateTimePicker::make('ends_at')
                                        ->label('Fim')
                                        ->seconds(false)
                                        ->required(),
                                    DateTimePicker::make('deadline_at')
                                        ->label('Prazo')
                                        ->seconds(false)
                                        ->required(),
                                ]),
                            Grid::make()
                                ->schema([

                                    Fieldset::make('Detalhes')
                                        ->columns(3)
                                        ->schema([
                                            Select::make('priority')
                                                ->label('')
                                                ->placeholder('Prioridade')
                                                ->options(TaskPriorityEnum::class)
                                                ->required(),

                                            Toggle::make('is_private')
                                                ->label('Privado?'),

                                            Toggle::make('is_urgent')
                                                ->label('Urgente?'),
                                        ]),
                                ]),
                        ]),
                ]),
        ];
    }
}
