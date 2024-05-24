<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\ClientTypeEnum;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClientResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClientResource\RelationManagers;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'CADASTROS';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'document';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['document', 'company.company', 'individual.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Cliente' => $record->company->company ?? $record->individual->name,
            'Telefone' => $record->company->phone ?? $record->individual->phone,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        if ($record->type == ClientTypeEnum::COMPANY) {
            return ClientCompanyResource::getUrl('edit', ['record' => $record]);
        }

        if ($record->type == ClientTypeEnum::INDIVIDUAL) {
            return ClientIndividualResource::getUrl('edit', ['record' => $record]);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->columns(5)
                    ->schema([

                        Forms\Components\Section::make()
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo de cliente')
                                    ->options(ClientTypeEnum::class)
                                    ->required(),

                                Forms\Components\TextInput::make('document')
                                    ->label('Documento')
                                    ->unique(table: Client::class)
                                    ->required()
                                    ->mask(RawJs::make(<<<'JS'
                                    $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                                JS))
                                    ->rule('cpf_ou_cnpj'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de cliente')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\ViewColumn::make('clients_exists')
                    ->label('Nome do cliente')
                    ->view('tables.columns.client-name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('document')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de cliente')
                    ->options(ClientTypeEnum::class),

            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            )
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Editar')
                    ->url(function (Client $record): string {
                        if ($record->type == ClientTypeEnum::COMPANY) {
                            return route('filament.admin.resources.client-companies.edit', $record);
                        } else {
                            return route('filament.admin.resources.client-individuals.edit', $record);
                        }
                    })
                    ->icon('heroicon-m-pencil-square'),

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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            // 'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
