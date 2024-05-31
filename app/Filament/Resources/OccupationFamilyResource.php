<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccupationFamilyResource\Pages;
use App\Filament\Resources\OccupationFamilyResource\RelationManagers;
use App\Models\OccupationFamily;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OccupationFamilyResource extends Resource
{
    protected static ?string $model = OccupationFamily::class;

    protected static ?string $navigationIcon = 'heroicon-m-code-bracket-square';

    protected static ?string $modelLabel = 'CBO - Família';

    protected static ?string $pluralModelLabel = 'CBOs - Famílias';

    protected static ?string $navigationGroup = 'TABELAS';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
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
            'index' => Pages\ListOccupationFamilies::route('/'),
            'create' => Pages\CreateOccupationFamily::route('/create'),
            'edit' => Pages\EditOccupationFamily::route('/{record}/edit'),
        ];
    }
}
