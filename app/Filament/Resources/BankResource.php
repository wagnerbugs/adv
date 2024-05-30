<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BankResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BankResource\RelationManagers;
use Leandrocfe\FilamentPtbrFormFields\Document;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $modelLabel = 'Banco';

    protected static ?string $pluralModelLabel = 'Bancos';

    protected static ?string $navigationGroup = 'TABELAS';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados do Banco (Esta tabela é atualizada dinamicamente)')
                    ->columns(3)
                    ->schema([

                        Forms\Components\Fieldset::make()
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('compe')
                                    ->label('COMPE')
                                    ->required()
                                    ->maxLength(3),
                                Forms\Components\TextInput::make('ispb')
                                    ->label('ISPB')
                                    ->required()
                                    ->maxLength(8),
                                Document::make('document')
                                    ->label('CNPJ')
                                    ->cnpj('99.999.999/9999-99')
                                    ->required()
                                    ->maxLength(18),
                            ]),

                        Forms\Components\Fieldset::make()
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('long_name')
                                    ->label('Nome Completo')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('short_name')
                                    ->label('Nome Abreviado')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
                                    ->label('Website')
                                    ->maxLength(255),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('compe')
                    ->label('COMPE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ispb')
                    ->label('ISPB')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document')
                    ->label('CNPJ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('short_name')
                    ->label('Nome Abreviado')
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString('<span style="text-transform:uppercase">' . $state . '</span>'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('Website')
                    ->icon('heroicon-m-globe-alt')
                    ->iconColor('primary')
                    ->url(fn (Bank $record): string => $record->url)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListBanks::route('/'),
            // 'create' => Pages\CreateBank::route('/create'),
            // 'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }
}
