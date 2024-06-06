<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\ClientTypeEnum;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use Leandrocfe\FilamentPtbrFormFields\Money;
use App\Filament\Resources\ProcessResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProcessResource\RelationManagers;
use App\Models\Court;
use App\Models\CourtDistrict;
use App\Models\CourtState;
use Carbon\Carbon;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processos';

    protected static ?string $navigationGroup = 'PROCESSOS';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('process')
                    ->label('Processo')
                    ->badge()
                    ->searchable(),

                Tables\Columns\ViewColumn::make('client_name')
                    ->label('Cliente')
                    ->view('tables.columns.client-name-process')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->with(['individual', 'company'])
                            ->where(function ($query) use ($search) {
                                $query->whereHas('individual', function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%");
                                })
                                    ->orWhereHas('company', function ($query) use ($search) {
                                        $query->where('company', 'like', "%{$search}%");
                                    })
                                    ->orWhere('clients.document', 'like', "%{$search}%");
                            });
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])
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
