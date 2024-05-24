<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\ClientTypeEnum;
use App\Models\ClientCompany;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClientCompanyResource\Pages;
use App\Filament\Resources\ClientCompanyResource\RelationManagers;

class ClientCompanyResource extends Resource
{
    protected static ?string $model = ClientCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Pessoa Jurídica';

    protected static ?string $pluralModelLabel = 'Pessoa Jurídica';

    protected static ?string $navigationGroup = 'CLIENTES';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('company')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->color('primary')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientCompany $record): string => $record->client->document),

                Tables\Columns\TextColumn::make('company_size')
                    ->label('Natureza Jurídica')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientCompany $record): string => $record->legal_nature),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Clientes ativos')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),

                Tables\Filters\Filter::make('company_size')
                    ->label('Tipo de empresa')
                    ->query(fn (Builder $query): Builder => $query->where('company_size', 'Matriz')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClientCompanies::route('/'),
            'create' => Pages\CreateClientCompany::route('/create'),
            'edit' => Pages\EditClientCompany::route('/{record}/edit'),
        ];
    }
}
