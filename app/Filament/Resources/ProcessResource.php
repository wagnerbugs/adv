<?php

namespace App\Filament\Resources;

use App\Enums\ClientTypeEnum;
use App\Filament\Resources\ProcessResource\Pages;
use App\Models\Client;
use App\Models\Process;
use App\Models\ProcessChat;
use App\Models\ProcessDetail;
use App\Models\ProcessMovement;
use App\Models\ProcessSubject;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processos';

    protected static ?string $navigationGroup = 'CLIENTES';

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
                    ->columns(1)
                    ->visibleOn('create')
                    ->schema([

                        Forms\Components\Section::make()
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Select::make('client_id')
                                    ->label('Cliente')
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
                                    ->addable(false)
                                    ->deletable(false)
                                    // ->reorderable(true)
                                    ->reorderableWithButtons()
                                    ->collapsed()
                                    ->relationship()
                                    ->schema([

                                        Forms\Components\Tabs::make('Tabs')

                                            ->columnSpanFull()
                                            ->contained(false)
                                            ->tabs([

                                                Forms\Components\Tabs\Tab::make('Dados do processo')
                                                    ->columns(3)
                                                    ->schema([

                                                        Forms\Components\Fieldset::make()
                                                            ->columns(2)
                                                            ->schema([

                                                                Forms\Components\Placeholder::make('client_process_number')
                                                                    ->label('Cliente')
                                                                    ->content(
                                                                        function (ProcessDetail $record): string {
                                                                            $client = Client::find($record->process->client_id);

                                                                            return $client->individual
                                                                                ? $client->individual->name.' ('.$client->document.')'
                                                                                : $client->company->company.' ('.$client->document.')';
                                                                        }
                                                                    ),

                                                                Forms\Components\Select::make('professionals')
                                                                    ->label('Profissionais')
                                                                    ->multiple()
                                                                    ->options(
                                                                        User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                                                                            ->where('user_profiles.is_active', true)
                                                                            ->pluck('users.name', 'users.id')
                                                                            ->toArray()
                                                                    ),

                                                            ]),

                                                        Forms\Components\Fieldset::make('CAPA DO PROCESSO')
                                                            ->schema([

                                                                Forms\Components\Placeholder::make('client_process_number')
                                                                    ->label('')
                                                                    ->content(fn (ProcessDetail $record): HtmlString => new HtmlString('Nº do processo: <strong class="text-violet-500">'.$record->process->process.'</strong>')),

                                                                Forms\Components\Placeholder::make('client_process_number_date_autation')
                                                                    ->label('')
                                                                    ->content(fn (ProcessDetail $record): HtmlString => new HtmlString(' Data de autuação: <strong class="text-violet-500">'.Carbon::parse($record->publish_date)->format('d/m/Y H:i:s').'</strong>')),

                                                                Forms\Components\Placeholder::make('judging_organ')
                                                                    ->label('')
                                                                    ->content(
                                                                        fn (ProcessDetail $record): HtmlString => new HtmlString('Órgão Julgador: <strong class="text-violet-500">'.$record->judging_name.'</strong>')
                                                                    ),

                                                                Forms\Components\Placeholder::make('class_name')
                                                                    ->label('')
                                                                    ->hint('Classe')
                                                                    ->hintIcon(
                                                                        'heroicon-m-information-circle',
                                                                        tooltip: fn (ProcessDetail $record): string => (
                                                                            $record->rule.' - '.$record->article.' - '.$record->class_description
                                                                        )
                                                                    )
                                                                    ->hintColor('warning')
                                                                    ->content(
                                                                        fn (ProcessDetail $record): HtmlString => new HtmlString('Classe da ação: <strong class="text-violet-500">'.$record->class_name.'</strong>')
                                                                    ),
                                                            ]),

                                                        Forms\Components\Fieldset::make('ASSUNTOS')
                                                            ->schema([

                                                                Forms\Components\Placeholder::make('subjects')
                                                                    ->label('')
                                                                    ->columnSpanFull()
                                                                    ->content(
                                                                        function (ProcessDetail $record): HtmlString {
                                                                            $subjects = ProcessSubject::where('process_detail_id', $record->id)->get();
                                                                            $subjectList = '<ul>';
                                                                            foreach ($subjects as $subject) {
                                                                                $subjectList .= '<li>'.$subject->code.' - <span class="text-violet-500 font-bold">'.$subject->name.'</span> ('.$subject->rule.' - '.$subject->article.')</li>';
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
                                                                            $movimentsList = '<div class="relative overflow-x-auto shadow-md sm:rounded-lg"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Data</th><th scope="col" class="px-6 py-3">Código</th><th scope="col" class="px-6 py-3">Movimento</th></tr></thead><tbody>';
                                                                            foreach ($moviments as $moviment) {
                                                                                $movimentsList .= '
                                                                                 <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">'.Carbon::parse($moviment->date)->format('d/m/Y H:i:s').'</th><td class="px-6 py-4">'.$moviment->code.'</td><td class="px-6 py-4 text-gray-900 dark:text-white font-bold">'.$moviment->name.'</td></tr>';
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

                                                // Forms\Components\Tabs\Tab::make('Anotações')
                                                //     ->schema([

                                                //         Forms\Components\Placeholder::make('chat')
                                                //             ->label('Histórico de procedimentos')
                                                //             ->live()
                                                //             ->content(
                                                //                 function (ProcessDetail $record): HtmlString {
                                                //                     if ($record->annotations) {

                                                //                         $chats = $record->annotations;
                                                //                         $chatList = '<div class="relative overflow-x-auto shadow-md sm:rounded-lg"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Data</th><th scope="col" class="px-6 py-3">Código</th><th scope="col" class="px-6 py-3">test</th></tr></thead><tbody>';
                                                //                         foreach ($chats as $chat) {
                                                //                             $chatList .=  '
                                                //                         <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . Carbon::parse($chat['date'])->format('d/m/Y H:i:s') . '</th><td class="px-6 py-4">' . $chat['author'] . '</td><td class="px-6 py-4">' . $chat['annotation'] . '</td></tr>
                                                //                         ';
                                                //                         }
                                                //                         $chatList .= '</tbody></table></div>';

                                                //                         return new HtmlString($chatList);
                                                //                     }
                                                //                     return new HtmlString('');
                                                //                 }
                                                //             ),
                                                //         Forms\Components\Repeater::make('annotations')
                                                //             ->label('Anotações')
                                                //             ->columns(2)
                                                //             ->collapsible()
                                                //             ->addActionLabel('Adicionar anotação')
                                                //             ->schema([
                                                //                 Forms\Components\Hidden::make('date')
                                                //                     ->label('Data')
                                                //                     ->default(Carbon::now('America/Sao_Paulo'))
                                                //                     ->disabled()
                                                //                     ->dehydrated(),
                                                //                 Forms\Components\Hidden::make('author')
                                                //                     ->label('Autor')
                                                //                     ->default(auth()->user()->name)
                                                //                     ->disabled()
                                                //                     ->dehydrated(),
                                                //                 Forms\Components\Textarea::make('annotation')
                                                //                     ->label('Nota')
                                                //                     ->required()
                                                //                     ->placeholder('Anotação...')
                                                //                     ->columnSpanFull(),
                                                //             ]),
                                                //     ]),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Forms\Components\Group::make()
                    ->visibleOn('edit')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Section::make('Histórico de procedimentos')
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
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"><img src="/storage/'.$history->user->profile->avatar.'" class="w-8 h-8 rounded-full" /></th><td class="px-6 py-4">'.$history->message.'</td><td class="px-6 py-4 text-gray-900 dark:text-white text-xs">'.Carbon::parse($history->created_at)->format('d/m/Y H:i:s').'</td></tr>';
                                                }
                                                $historiesList .= '</tbody></table></div>';

                                                return new HtmlString($historiesList);
                                            }

                                            return new HtmlString('Sem histórico');
                                        }
                                    ),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\ViewColumn::make('details.status_process_detail')
                //     ->label('Progresso')
                //     ->view('tables.columns.status-process-detail'),

                Tables\Columns\ViewColumn::make('cliente')
                    ->label('Cliente')
                    ->view('tables.columns.process-client-name'),

                Tables\Columns\ViewColumn::make('details.professionals')
                    ->label('Profissionais')
                    ->view('tables.columns.process-detail-professionals'),

                Tables\Columns\TextColumn::make('process')
                    ->label('Processo')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('details.class_name')
                    ->label('Classe processual')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('details.judging_name')
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
