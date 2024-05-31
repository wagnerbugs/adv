<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateCourtResource\Pages;
use App\Filament\Resources\StateCourtResource\RelationManagers;
use App\Models\StateCourt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StateCourtResource extends Resource
{
    protected static ?string $model = StateCourt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Tribunal Estatual';

    protected static ?string $pluralModelLabel = 'Tribunais Estatais';

    protected static ?string $navigationGroup = 'TABELAS';

    protected static ?int $navigationSort = 101;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('abbreviation')
                    ->label('Sigla')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->label('Endpoint')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo?')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abbreviation')
                    ->label('Sigla')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo?'),
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
            'index' => Pages\ListStateCourts::route('/'),
            'create' => Pages\CreateStateCourt::route('/create'),
            'edit' => Pages\EditStateCourt::route('/{record}/edit'),
        ];
    }
}
