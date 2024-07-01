<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProcessChat;
use App\Enums\ClientTypeEnum;
use App\Models\ProcessDetail;
use App\Models\ProcessSubject;
use App\Models\ProcessMovement;
use App\Traits\CapitalizeTrait;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\ActionSize;
use App\Jobs\UpdateProcessDetailsEprocJob;
use App\Filament\Resources\ProcessResource\Pages;

class ProcessResource extends Resource
{
    use CapitalizeTrait;

    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processos';

    protected static ?string $navigationGroup = 'CLIENTES';

    // protected static ?string $navigationParentItem = 'Clientes';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpan(2)
                    ->visibleOn('create')
                    ->schema([

                        Forms\Components\Section::make()
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Select::make('clients')
                                    ->label('Cliente')
                                    ->multiple()
                                    ->options(function () {
                                        $clients = Client::with(['individual', 'company'])->get();
                                        $options = [];

                                        foreach ($clients as $client) {
                                            if ($client->type === ClientTypeEnum::INDIVIDUAL && $client->individual) {
                                                $options[$client->id] = $client->individual->name;
                                            } elseif ($client->type === ClientTypeEnum::COMPANY && $client->company) {
                                                $options[$client->id] = $client->company->company;
                                            }
                                        }

                                        return $options;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('process')
                                    ->required()
                                    ->mask('9999999-99.9999.9.99.9999')
                                    ->unique(table: 'processes', column: 'process', ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Group::make()
                    ->visibleOn('edit')
                    ->columnSpan(3)
                    ->schema([

                        Forms\Components\section::make()

                            ->schema([

                                Forms\Components\Repeater::make('details')
                                    ->label('Processo')
                                    ->itemLabel(fn (array $state): ?string => $state['process_api_id'] ?? null)
                                    ->addable(false)
                                    ->deletable(false)
                                    // ->reorderable(true)
                                    // ->reorderableWithButtons()
                                    ->collapsible()
                                    ->relationship()
                                    ->schema([

                                        Forms\Components\Tabs::make('Tabs')

                                            ->columnSpanFull()
                                            ->contained(false)
                                            ->tabs([

                                                Forms\Components\Tabs\Tab::make('Dados do processo')
                                                    ->columns(3)
                                                    ->schema([

                                                        Forms\Components\Section::make('Dados do processo')
                                                            ->compact()
                                                            ->headerActions([
                                                                Forms\Components\Actions\Action::make('eproc')
                                                                    ->visible(
                                                                        fn (ProcessDetail $record): bool => ($record->process->court_state_code == 27) ? true : false
                                                                    )
                                                                    ->label('Eproc')
                                                                    ->icon('heroicon-o-cloud')
                                                                    ->action(
                                                                        fn (ProcessDetail $record) =>
                                                                        UpdateProcessDetailsEprocJob::dispatch($record->id)
                                                                    )
                                                                    ->size(ActionSize::ExtraSmall),
                                                            ])
                                                            ->columns(5)
                                                            ->schema([

                                                                Forms\Components\Placeholder::make('client_process_number')
                                                                    ->label('Cliente')
                                                                    ->columnSpan(3)
                                                                    ->content(
                                                                        function (ProcessDetail $record): HtmlString {
                                                                            $clients = Client::find($record->process->clients);

                                                                            // dd($clients);
                                                                            $client_list = '';
                                                                            foreach ($clients as $client) {
                                                                                if ($client->type === ClientTypeEnum::INDIVIDUAL && $client->individual) {
                                                                                    $client_list .= $client->individual->name . ' (' . $client->document . ')<br>';
                                                                                } elseif ($client->type === ClientTypeEnum::COMPANY && $client->company) {
                                                                                    $client_list .= $client->company->company . ' (' . $client->document . ')<br>';
                                                                                }
                                                                            }
                                                                            return new HtmlString($client_list);

                                                                            // return $client->individual
                                                                            //     ? $client->individual->name . ' (' . $client->document . ')'
                                                                            //     : $client->company->company . ' (' . $client->document . ')';
                                                                        }
                                                                    ),

                                                                Forms\Components\Select::make('professionals')
                                                                    ->label('Responsáveis')
                                                                    ->columnSpan(2)
                                                                    ->multiple()
                                                                    ->options(
                                                                        User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                                                                            ->where('user_profiles.is_active', true)
                                                                            ->pluck('users.name', 'users.id')
                                                                            ->toArray()
                                                                    ),

                                                            ]),

                                                        Forms\Components\Section::make(
                                                            fn (ProcessDetail $record): HtmlString =>
                                                            new HtmlString(
                                                                '<div class="flex justify-between"><div><span>Capa do processo</span></div> <div class=""><span class="text-sm text-white"> Situação atual: </span><span class="text-sm text-violet-500">' .
                                                                    $record->current_situation
                                                                    . '</span></div>'
                                                            )
                                                        )
                                                            ->compact()
                                                            ->collapsible()
                                                            ->columns(2)
                                                            ->schema([

                                                                Forms\Components\Placeholder::make('client_process_number')
                                                                    ->label('')
                                                                    ->content(fn (ProcessDetail $record): HtmlString => new HtmlString('Nº do processo: <strong class="text-violet-500">' . $record->process->process . '</strong>')),

                                                                Forms\Components\Placeholder::make('client_process_number_date_autation')
                                                                    ->label('')
                                                                    ->content(fn (ProcessDetail $record): HtmlString => new HtmlString(' Data de autuação: <strong class="text-violet-500">' . Carbon::parse($record->publish_date)->format('d/m/Y H:i:s') . '</strong>')),

                                                                Forms\Components\Placeholder::make('judging_organ')
                                                                    ->label('')
                                                                    ->columnSpanFull()
                                                                    ->content(
                                                                        fn (ProcessDetail $record): HtmlString => new HtmlString('Órgão Julgador: <strong class="text-violet-500 uppercase">' . $record->judging_name . '</strong>')
                                                                    ),

                                                                Forms\Components\Placeholder::make('magistrate')
                                                                    ->label('')
                                                                    ->columnSpanFull()
                                                                    ->content(
                                                                        fn (ProcessDetail $record): HtmlString => new HtmlString('Juiz(a): <strong class="text-violet-500">' . $record->magistrate . '</strong>')
                                                                    ),



                                                            ]),

                                                        Forms\Components\Section::make('Partes e Representações')
                                                            ->collapsible()
                                                            ->collapsed()
                                                            ->schema([
                                                                Forms\Components\Placeholder::make('parties_and_representatives')
                                                                    ->label('')
                                                                    ->content(
                                                                        function ($record): HtmlString {
                                                                            $parties = $record->parties_and_representatives;
                                                                            $active_pole = $record->active_pole;
                                                                            $passive_pole = $record->passive_pole;
                                                                            $content = '';
                                                                            if (!empty($parties)) {
                                                                                $firstParties = implode('<br>', $parties['first_parties']);
                                                                                $secondParties = implode('<br>', $parties['second_parties']);

                                                                                $content = "
                                                                                    <div>
                                                                                        <h3 class='text-sm font-bold text-violet-500'>{$active_pole}</h3>
                                                                                        <p class='text-xs'>{$firstParties}</p>
                                                                                        <h3 class='text-sm font-bold text-violet-500'>{$passive_pole}</h3>
                                                                                        <p class='text-xs'>{$secondParties}</p>
                                                                                    </div>
                                                                                ";
                                                                            }


                                                                            return new HtmlString($content);
                                                                        }
                                                                    ),
                                                            ]),

                                                        Forms\Components\Section::make('Informações adicionais')
                                                            ->collapsible()
                                                            ->collapsed()
                                                            ->schema([
                                                                Forms\Components\Placeholder::make('additional_information_placeholder')
                                                                    ->label('')
                                                                    ->content(
                                                                        function ($record): HtmlString {
                                                                            $additionalInfo = $record->additional_information;
                                                                            $content = "<div class='flex flex-wrap'>";
                                                                            if (!empty($additionalInfo)) {
                                                                                foreach ($additionalInfo as $key => $value) {
                                                                                    $content .= "<div class='pl-4'><strong>{$key}:</strong> <span class='text-violet-500'>{$value}</span></div>";
                                                                                }
                                                                            }

                                                                            $content .= "</div>";

                                                                            return new HtmlString($content);
                                                                        }
                                                                    ),
                                                            ]),

                                                        Forms\Components\Section::make(fn (ProcessDetail $record): string => $record->class_name)
                                                            ->collapsible()
                                                            ->collapsed()
                                                            ->columns(1)
                                                            ->schema([
                                                                Forms\Components\Placeholder::make('class_name')
                                                                    ->label('')
                                                                    ->content(
                                                                        fn (ProcessDetail $record): HtmlString => new HtmlString('Classe da ação: <strong class="text-violet-500">' . $record->class_name . '</strong><br><span class="text-gray-400">' . $record->rule . ' - ' . $record->article . ' - ' . $record->class_description . '</span>')
                                                                    ),
                                                            ]),
                                                        Forms\Components\Section::make('Assuntos')
                                                            ->collapsed()
                                                            ->collapsible()
                                                            ->schema([

                                                                Forms\Components\Placeholder::make('subjects')
                                                                    ->label('')
                                                                    ->columnSpanFull()
                                                                    ->content(
                                                                        function (ProcessDetail $record): HtmlString {
                                                                            $subjects = ProcessSubject::where('process_detail_id', $record->id)->get();
                                                                            $subjectList = '<ul>';
                                                                            foreach ($subjects as $subject) {
                                                                                $subjectList .= '<li>' . $subject->code . ' - <span class="text-violet-500 font-bold">' . $subject->name . '</span> (' . $subject->rule . ' - ' . $subject->article . ')</li>';
                                                                            }
                                                                            $subjectList .= '</ul>';

                                                                            return new HtmlString($subjectList);
                                                                        }
                                                                    ),

                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Movimentos')
                                                    ->schema([
                                                        Forms\Components\Fieldset::make('MOVIMENTOS')
                                                            ->schema([
                                                                Forms\Components\Placeholder::make('moviments_list')
                                                                    ->label('')
                                                                    ->columnSpanFull()
                                                                    ->content(
                                                                        function (ProcessDetail $record): HtmlString {
                                                                            $moviments = ProcessMovement::where('process_detail_id', $record->id)->orderBy('date', 'desc')->get();
                                                                            $movimentsList = '<div class="relative overflow-x-auto shadow-md sm:rounded-lg"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">#</th><th scope="col" class="px-6 py-3">Data</th><th scope="col" class="px-6 py-3">Código</th><th scope="col" class="px-6 py-3">Movimento</th></tr></thead><tbody>';
                                                                            $counter = $moviments->count();
                                                                            foreach ($moviments as $moviment) {
                                                                                $movimentsList .= '
                                                                                 <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><td class="px-6 py-4">' . $counter . '</td><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . Carbon::parse($moviment->date)->format('d/m/Y H:i:s') . '</th><td class="px-6 py-4">' . $moviment->code . '</td><td class="px-6 py-4 text-gray-900 dark:text-white font-bold">' . $moviment->name . '</td></tr>';
                                                                                $counter--;
                                                                            }
                                                                            $movimentsList .= '</tbody></table></div>';

                                                                            return new HtmlString($movimentsList);
                                                                        }
                                                                    ),
                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Arquivos')
                                                    ->schema([
                                                        Forms\Components\Repeater::make('attachments')
                                                            ->label('Arquivos')
                                                            ->collapsed()
                                                            ->grid(2)
                                                            ->addActionLabel('Anexar arquivo')
                                                            ->schema([
                                                                Forms\Components\TextInput::make('title')
                                                                    ->label('Título do arquivo')
                                                                    ->placeholder('Descrição curta do documento')
                                                                    ->maxLength(255),
                                                                Forms\Components\FileUpload::make('file')
                                                                    ->label('Arquivo')
                                                                    ->directory('employees'),
                                                            ]),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Forms\Components\Group::make()
                    ->visibleOn('edit')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Tabs::make()
                            ->tabs([

                                Forms\Components\Tabs\Tab::make('Procedimentos')
                                    ->schema([

                                        Forms\Components\Placeholder::make('history')
                                            ->label('')
                                            ->live()
                                            ->content(
                                                function (Process $record): HtmlString {
                                                    $histories = ProcessChat::where('process_id', $record->id)->orderBy('created_at', 'desc')->get();

                                                    $historiesList = '<div class="relative overflow-x-auto shadow-md sm:rounded-lg"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Autor</th><th scope="col" class="px-6 py-3">Registro</th><th scope="col" class="px-6 py-3">Data</th></tr></thead><tbody>';
                                                    if ($histories) {
                                                        foreach ($histories as $history) {
                                                            $historiesList .= '
                                                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"><img src="' . $history->user->getFilamentAvatarUrl() . '" class="w-8 h-8 rounded-full" alt="' . $history->user->name . '" title="' . $history->user->name . '"/></th><td class="px-6 py-4">' . $history->message . ' <br></td><td class="px-6 py-4 text-gray-900 dark:text-white text-xs">' . Carbon::parse($history->created_at)->format('d/m/Y H:i:s') . '</td></tr>';
                                                        }
                                                        $historiesList .= '</tbody></table></div>';

                                                        return new HtmlString($historiesList);
                                                    }

                                                    return new HtmlString('Sem histórico');
                                                }
                                            ),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Acordos')
                                    ->schema([]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('12s')
            ->columns([

                // Tables\Columns\ViewColumn::make('cliente')
                //     ->label('Cliente')
                //     ->view('tables.columns.process-client-name'),

                Tables\Columns\ViewColumn::make('details.professionals')
                    ->label('Profissionais')
                    ->view('tables.columns.process-detail-professionals'),

                Tables\Columns\TextColumn::make('process')
                    ->label('Processo')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('details.class_name')
                    ->placeholder(new HtmlString('
                        <div class="flex items-center  h-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 4">
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="0" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="1" begin="-.5"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="25" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="2" begin="-.4"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="50" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="3" begin="-.3"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="75" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="4" begin="-.2"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="100" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="5" begin="-.1"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="125" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="6" begin="0"></animate>
                                </rect>
                            </svg>
                        </div>
                    '))
                    ->label('Classe processual')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('details.judging_name')
                    ->label('Órgão Julgador')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('details.publish_date')
                    ->label('Data de publicação')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('details.last_modification_date')
                    ->label('Data de modificação')
                    ->badge()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcesses::route('/'),
            'create' => Pages\CreateProcess::route('/create'),
            'edit' => Pages\EditProcess::route('/{record}/edit'),
        ];
    }
}
