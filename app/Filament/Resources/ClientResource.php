<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\ClientTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\FinancialPayment;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClientResource\Pages;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'CADASTROS';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public Client $teste;

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

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de cliente')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                // Tables\Columns\TextColumn::make('name')
                //     ->label('Cliente')
                //     ->description(fn (Client $record): string => $record->document)
                //     ->searchable()
                //     ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo'),

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
            ->defaultSort('id', 'desc')

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


                Action::make('Editar')
                    ->icon('heroicon-m-pencil-square')
                    ->url(function (Client $record): string {
                        if ($record->type == ClientTypeEnum::COMPANY) {
                            return route('filament.admin.resources.client-companies.edit', $record->company->id);
                        } else {
                            return route('filament.admin.resources.client-individuals.edit', $record->individual->id);
                        }
                    }),

                Action::make('payment')
                    ->label('Pagamentos')
                    ->color('secondary')
                    ->icon('heroicon-o-currency-dollar')
                    ->model(FinancialPayment::class)
                    ->form([
                        Forms\Components\Fieldset::make()
                            ->schema([
                                Forms\Components\Hidden::make('user_id')
                                    ->label('Usuário')
                                    ->default(auth()->user()->id),

                                Forms\Components\Hidden::make('client_id')
                                    ->label('Usuário')
                                    ->default(
                                        fn (Client $record): int => $record->id
                                    ),

                                Forms\Components\Select::make('process_id')
                                    ->label('Processo')
                                    ->options(function (Client $record) {
                                        $processes = Process::where('client_id', $record->id)->get();
                                        return $processes->pluck('process', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Total')
                                    ->prefix('R$')
                                    ->numeric()
                                    ->required(),
                            ]),

                        Forms\Components\Fieldset::make('Entrada')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Select::make('entry_payment_method')
                                    ->label('Método de pagamento')
                                    ->options(PaymentMethodEnum::class)
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('entry_amount')
                                    ->label('Valor de entrada')
                                    ->prefix('R$')
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => $set('installment_amount', $get('amount') - $state))
                                    ->required(),

                                Forms\Components\DatePicker::make('entry_date')
                                    ->label('Data da entrada')
                                    ->default(Carbon::now())
                                    ->required(),
                            ]),

                        Forms\Components\Fieldset::make('Parcelamento')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('installments')
                                    ->label('Parcelas')
                                    ->suffixIcon('heroicon-o-x-mark')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->required()
                                    ->minValue(0)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?int $state, ?int $old): void {
                                        if (is_null($state)) {
                                            $set('payments', []);
                                            return;
                                        }

                                        $installmentAmount = $get('installment_amount');
                                        $payments = [];
                                        for ($i = 0; $i < $state; $i++) {
                                            $payments[] = [
                                                'amount_installment' => $installmentAmount / max($state, 1),
                                                'due_date_installment' => Carbon::parse($get('base_date_installment'))->addMonths($i + 1)->format('Y-m-d')
                                            ];
                                        }

                                        $set('payments', $payments);
                                    })
                                    ->afterStateHydrated(function (Set $set, Get $get) {
                                        $set('installments', 0);
                                    }),

                                Forms\Components\TextInput::make('installment_amount')
                                    ->label('Restante a parcelar')
                                    ->prefix('R$')
                                    ->numeric('0.00')
                                    ->required(),

                                Forms\Components\DatePicker::make('base_date_installment')
                                    ->label('Data base para parcelas')
                                    ->default(Carbon::now()->format('Y-m-d'))
                                    ->required(),
                            ]),

                        Forms\Components\Fieldset::make('Parcelas')

                            ->schema([
                                Forms\Components\Repeater::make('payments')
                                    // ->relationship('installments')
                                    ->hiddenLabel()
                                    ->deletable(false)
                                    ->addable(false)
                                    ->columnSpan(3)
                                    ->reorderable(false)
                                    ->columns(3)
                                    ->defaultItems(function (Get $get) {
                                        return $get('installments');
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('amount_installment')
                                            ->label('Valor da parcela')
                                            ->prefix('R$')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->required(),

                                        Forms\Components\DatePicker::make('due_date_installment')
                                            ->label('Vencimento da parcela')
                                            ->required(),
                                    ])
                            ]),
                    ])


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

    protected function getRedirectUrl(): string
    {
        dd($this->getRecord());
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
