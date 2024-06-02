<?php

namespace App\Filament\Resources;

use App\Enums\ClientTypeEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProcessResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProcessResource\RelationManagers;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processos';

    protected static ?string $navigationGroup = 'PROCESSOS';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
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
                    ]),

                Forms\Components\TextInput::make('process')
                    ->required()
                    ->mask('9999999-99.9999.9.99.9999')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('process')
                    ->searchable(),
                Tables\Columns\TextColumn::make('process_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('process_digit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('process_year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('court_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('court_state_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('court_disctric_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nature')
                    ->searchable(),
                Tables\Columns\TextColumn::make('active_pole')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passive_pole')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rule')
                    ->searchable(),
                Tables\Columns\TextColumn::make('article')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publish_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_modification_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('secrecy_level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
