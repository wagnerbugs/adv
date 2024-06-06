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

    protected static ?string $modelLabel = 'Serventia';

    protected static ?string $pluralModelLabel = 'Serventias';

    protected static ?string $navigationGroup = 'TABELAS';

    protected static ?int $navigationSort = 102;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('court')
                    ->label('Tribunal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->label('UF')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Município')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('service_number')
                    ->label('Serventia')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('service_name')
                    ->label('Nome da Serventia')
                    ->maxLength(255),
                Forms\Components\TextInput::make('district_code')
                    ->label('Origem')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->label('Tipo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit')
                    ->label('Unidade')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefone')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Endereço')
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->maxLength(255),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('district_code')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('court')
                    ->label('Tribunal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_number')
                    ->label('Código Serventia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Nome')
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
            'index' => Pages\ListCourtDistricts::route('/'),
            'create' => Pages\CreateCourtDistrict::route('/create'),
            'edit' => Pages\EditCourtDistrict::route('/{record}/edit'),
        ];
    }
}
