<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FinancialAccount;
use Filament\Resources\Resource;
use App\Enums\TypeOfBankAccountEnum;
use App\Filament\Resources\FinancialAccountResource\Pages;

class FinancialAccountResource extends Resource
{
    protected static ?string $model = FinancialAccount::class;

    protected static ?string $pluralModelLabel = 'Contas';

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'FINANCEIRO';

    protected static ?string $navigationLabel = 'Contas';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpan(3)
                    ->schema([

                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Dados da Conta')
                                    ->schema([
                                        Forms\Components\Fieldset::make()
                                            ->columns(3)
                                            ->schema([

                                                Forms\Components\Select::make('bancos')
                                                    ->label('Banco')
                                                    ->visibleOn('create')
                                                    ->helperText('Caso seja banco, busque pelo nome')
                                                    ->options(Bank::all()->pluck('long_name', 'long_name'))
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('name', $state)),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome da conta')
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('balance')
                                                    ->label('Saldo')
                                                    ->prefix('R$')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0.00),

                                            ]),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Descrição')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Dados bancários')
                                    ->visible(fn (Get $get): bool => $get('is_bank'))
                                    ->schema([
                                        Forms\Components\Fieldset::make('Conta bancária')
                                            ->columns(4)
                                            ->schema([
                                                Forms\Components\Select::make('type_account_bank')
                                                    ->label('Tipo de conta')
                                                    ->columnSpan(1)
                                                    ->options(TypeOfBankAccountEnum::class),
                                                Forms\Components\Select::make('bank_name')
                                                    ->label('Banco')
                                                    ->columnSpan(3)
                                                    ->options(Bank::all()->map(function ($bank) {
                                                        return strtoupper($bank->compe . ' - ' . $bank->long_name);
                                                    }))
                                                    ->searchable(),
                                                Forms\Components\TextInput::make('bank_agency')
                                                    ->label('Agência')
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('bank_account')
                                                    ->label('Conta')
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('pix')
                                                    ->label('PIX')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ])
                    ]),

                Forms\Components\Group::make()
                    ->columnSpan(1)
                    ->schema([

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_bank')
                                    ->label('É conta bancária?')
                                    ->live()
                                    ->default(false),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('É ativa?')
                                    ->default(true),
                            ]),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Saldo')
                    ->money('BRL', locale: 'pt_BR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_bank')
                    ->label('Conta bancária'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativa'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
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
            'index' => Pages\ListFinancialAccounts::route('/'),
            'create' => Pages\CreateFinancialAccount::route('/create'),
            'edit' => Pages\EditFinancialAccount::route('/{record}/edit'),
        ];
    }
}
