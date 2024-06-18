<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourtStateResource\Pages;
use App\Models\CourtState;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourtStateResource extends Resource
{
    protected static ?string $model = CourtState::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Estatual';

    protected static ?string $pluralModelLabel = 'Estaduais';

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
                Forms\Components\TextInput::make('court')
                    ->label('Tribunal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->label('Estado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->label('Descricão')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('court')
                    ->label('Tribunal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('UF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descricão')
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
            'index' => Pages\ListCourtStates::route('/'),
            'create' => Pages\CreateCourtState::route('/create'),
            'edit' => Pages\EditCourtState::route('/{record}/edit'),
        ];
    }
}
