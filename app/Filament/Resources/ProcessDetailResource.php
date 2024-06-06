<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Court;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Form;
use App\Models\CourtState;
use Filament\Tables\Table;
use App\Models\CourtDistrict;
use App\Models\ProcessDetail;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Imports\ImportColumn;
use Leandrocfe\FilamentPtbrFormFields\Money;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProcessDetailResource\Pages;
use App\Filament\Resources\ProcessDetailResource\RelationManagers;
use App\Models\ProcessMovement;
use App\Models\ProcessSubject;
use Illuminate\Support\HtmlString;

class ProcessDetailResource extends Resource
{
    protected static ?string $model = ProcessDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'process_api_id';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpan(2)
                    ->schema([

                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([

                                Forms\Components\Tabs\Tab::make('Processo')
                                    ->schema([

                                        Forms\Components\Section::make()
                                            ->columns(2)
                                            ->schema([

                                                Forms\Components\Fieldset::make('CLIENTE')
                                                    ->schema([

                                                        Forms\Components\Placeholder::make('client_process_number')
                                                            ->label('')
                                                            ->content(
                                                                function (ProcessDetail $record): string {
                                                                    $client = Client::find($record->process->client_id);
                                                                    return $client->individual
                                                                        ? $client->individual->name . ' (' . $client->document . ')'
                                                                        : $client->company->company . ' (' . $client->document . ')';
                                                                }
                                                            ),
                                                    ]),

                                                Forms\Components\Fieldset::make('CAPA DO PROCESSO')
                                                    ->schema([


                                                        Forms\Components\Placeholder::make('client_process_number')
                                                            ->label('')
                                                            ->content(fn (ProcessDetail $record): HtmlString => new HtmlString('Nº do processo: <strong class="text-violet-500">' . $record->process->process . '</strong>')),

                                                        Forms\Components\Placeholder::make('client_process_number_date_autation')
                                                            ->label('')
                                                            ->content(fn (ProcessDetail $record): HtmlString => new HtmlString(' Data de autuação: <strong class="text-violet-500">' .  Carbon::parse($record->publish_date)->format('d/m/Y H:i:s') . '</strong>')),

                                                        Forms\Components\Placeholder::make('judging_organ')
                                                            ->label('')
                                                            ->content(
                                                                fn (ProcessDetail $record): HtmlString => new HtmlString('Órgão Julgador: <strong class="text-violet-500">' . $record->judging_name . '</strong>')
                                                            ),

                                                        Forms\Components\Placeholder::make('class_name')
                                                            ->label('')
                                                            ->hint('Classe')
                                                            ->hintIcon(
                                                                'heroicon-m-information-circle',
                                                                tooltip: fn (ProcessDetail $record): string => (
                                                                    $record->rule . ' - ' . $record->article . ' - ' . $record->class_description
                                                                )
                                                            )
                                                            ->hintColor('warning')
                                                            ->content(
                                                                fn (ProcessDetail $record): HtmlString => new HtmlString('Classe da ação: <strong class="text-violet-500">' . $record->class_name . '</strong>')
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
                                                                        $subjectList .=  '<li>' . $subject->code . ' - <span class="text-violet-500 font-bold">' . $subject->name . '</span> (' . $subject->rule . ' - ' . $subject->article . ')</li>';
                                                                    }
                                                                    $subjectList .= '</ul>';

                                                                    return new HtmlString($subjectList);
                                                                }
                                                            ),

                                                    ]),
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
                                                                $movimentsList .=  '
                                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . Carbon::parse($moviment->date)->format('d/m/Y H:i:s') . '</th><td class="px-6 py-4">' . $moviment->code . '</td><td class="px-6 py-4 text-gray-900 dark:text-white font-bold">' . $moviment->name . '</td></tr>';
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

                                Forms\Components\Tabs\Tab::make('Anotações')
                                    ->schema([
                                        Forms\Components\Repeater::make('annotations')
                                            ->label('Anotações')
                                            ->columns(2)
                                            ->collapsed()
                                            ->deletable(false)
                                            ->addActionLabel('Adicionar anotação')
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('date')
                                                    ->label('Data')
                                                    ->default(now())
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make('author')
                                                    ->label('Autor')
                                                    ->default(auth()->user()->name)
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make('annotation')
                                                    ->label('Nota')
                                                    ->disabled(!auth()->user()->hasRole('Root'))
                                                    ->required()
                                                    ->placeholder('Anotação...')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->visibleOn('edit')
                    ->columns(1)
                    ->schema([

                        Forms\Components\Section::make()
                            ->schema([

                                Forms\Components\Fieldset::make('Imagem')
                                    ->schema([]),

                                Forms\Components\Fieldset::make('Status')
                                    ->schema([]),

                            ]),


                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('status_process_detail')
                    ->label('Progresso')
                    ->view('tables.columns.status-process-detail'),
                Tables\Columns\TextColumn::make('process.process')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('class_name')
                    ->badge(true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('judging_name')
                    ->description(fn (ProcessDetail $record): string => $record->process_api_id)
                    ->searchable(),
                Tables\Columns\TextColumn::make('publish_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_modification_date')
                    ->dateTime()
                    ->sortable(),

            ])
            ->defaultSort('updated_at', 'desc')
            ->poll('5s')
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
            'index' => Pages\ListProcessDetails::route('/'),
            'create' => Pages\CreateProcessDetail::route('/create'),
            'edit' => Pages\EditProcessDetail::route('/{record}/edit'),
        ];
    }
}
