<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientIndividualResource\Pages;
use App\Models\ClientIndividual;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientIndividualResource extends Resource
{
    protected static ?string $model = ClientIndividual::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Pessoa Física';

    protected static ?string $pluralModelLabel = 'Pessoa Física';

    protected static ?string $navigationGroup = 'CLIENTES';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientIndividual $record): string => $record->client->document),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable(),

                Tables\Columns\TextColumn::make('state')
                    ->label('UF')
                    ->searchable(),
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
            'index' => Pages\ListClientIndividuals::route('/'),
            'create' => Pages\CreateClientIndividual::route('/create'),
            'edit' => Pages\EditClientIndividual::route('/{record}/edit'),
        ];
    }
}
