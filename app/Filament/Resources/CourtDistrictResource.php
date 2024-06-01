<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourtDistrictResource\Pages;
use App\Filament\Resources\CourtDistrictResource\RelationManagers;
use App\Models\CourtDistrict;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourtDistrictResource extends Resource
{
    protected static ?string $model = CourtDistrict::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Comarca';

    protected static ?string $pluralModelLabel = 'Comarcas';

    protected static ?string $navigationGroup = 'TABELAS';

    protected static ?int $navigationSort = 102;

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
                Forms\Components\TextInput::make('district')
                    ->label('Distrito')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->label('Tipo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('classification')
                    ->label('Classificação')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo'),
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
                Tables\Columns\TextColumn::make('district')
                    ->label('Distrito')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Ativo?')
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
            'index' => Pages\ListCourtDistricts::route('/'),
            'create' => Pages\CreateCourtDistrict::route('/create'),
            'edit' => Pages\EditCourtDistrict::route('/{record}/edit'),
        ];
    }
}
