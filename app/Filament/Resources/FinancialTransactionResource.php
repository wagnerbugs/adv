<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\PaymentMethodEnum;
use Filament\Resources\Resource;
use App\Models\FinancialCategory;
use App\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionStatusEnum;
use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FinancialTransactionResource\Pages;
use App\Filament\Resources\FinancialTransactionResource\RelationManagers;
use App\Models\FinancialAccount;

class FinancialTransactionResource extends Resource
{
    protected static ?string $model = FinancialTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $modelLabel = 'Transações';

    protected static ?string $pluralModelLabel = 'Transações';

    protected static ?string $navigationGroup = 'FINANCEIRO';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([

                        Forms\Components\Fieldset::make()
                            ->columns(4)
                            ->schema([

                                Forms\Components\Hidden::make('user_id')
                                    ->default(auth()->id())
                                    ->required(),

                                Forms\Components\Select::make('financial_account_id')
                                    ->label('Conta')
                                    ->relationship('account', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\Group::make()
                                            ->columns(2)
                                            ->schema([
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
                                            ])
                                    ]),

                                Forms\Components\Select::make('type')
                                    ->label('Tipo')
                                    ->searchable()
                                    ->preload()
                                    ->options(TransactionTypeEnum::class)
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('financial_category_id', null))
                                    ->required(),

                                Forms\Components\Select::make('financial_category_id')
                                    ->label('Categoria')
                                    ->searchable()
                                    ->preload()
                                    ->loadingMessage('Carregando opções...')
                                    ->options(function (callable $get) {
                                        $type = $get('type');
                                        if ($type === null) {
                                            return [];
                                        }
                                        return FinancialCategory::getHierarchicalOptions($type);
                                    })
                                    ->reactive()
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Situação')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->options(TransactionStatusEnum::class),
                            ]),

                        Forms\Components\Fieldset::make('Relacionamentos')
                            ->columns(4)
                            ->schema([
                                Forms\Components\Select::make('client_id')
                                    ->label('Cliente')
                                    ->options(function (): array {
                                        $clients = Client::all();
                                        $clientsList = [];

                                        if ($clients) {
                                            foreach ($clients as $client) {
                                                $clientsList[$client->id] = '<span class="me-3 text-sm font-medium">' . $client->name . '</span><br><span class="me-3 text-xs text-gray-400">' . $client->document . '</span>';
                                            }
                                        }

                                        return $clientsList;
                                    })
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return Client::where('document', 'like', "%{$search}%")
                                            ->orWhere(function ($query) use ($search) {
                                                $query
                                                    ->whereHas('individual', function (Builder $individualQuery) use ($search) {
                                                        $individualQuery->where('name', 'like', "%{$search}%");
                                                    })
                                                    ->orWhereHas('company', function (Builder $companyQuery) use ($search) {
                                                        $companyQuery->where('company', 'like', "%{$search}%");
                                                    });
                                            })
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(function ($client) {
                                                return [$client->id => "<span class='me-3 text-sm font-medium'>{$client->name}</span><br><span class='me-3 text-xs text-gray-400'>{$client->document}</span>"];
                                            })
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->allowHtml(),

                                Forms\Components\Select::make('process_id')
                                    ->label('Processo')

                                    ->searchable()
                                    ->options(function (): array {
                                        $processes = Process::with('client')->get();
                                        $processesList = [];

                                        if ($processes) {
                                            foreach ($processes as $process) {
                                                $processesList[$process->id] =
                                                    '
                                            <span class="me-3 text-sm font-medium">' .
                                                    $process->client->name .
                                                    '</span><br><span class="me-3 text-xs text-gray-400">' .
                                                    $process->process .
                                                    '</span>';
                                            }
                                        }

                                        return $processesList;
                                    })
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return Process::whereHas('client', function (Builder $clientQuery) use ($search) {
                                            $clientQuery->where(function ($query) use ($search) {
                                                $query
                                                    ->whereHas('individual', function (Builder $individualQuery) use ($search) {
                                                        $individualQuery->where('name', 'like', "%{$search}%");
                                                    })
                                                    ->orWhereHas('company', function (Builder $companyQuery) use ($search) {
                                                        $companyQuery->where('company', 'like', "%{$search}%");
                                                    });
                                            });
                                        })
                                            ->orWhere('process', 'like', "%{$search}%")
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(function ($item) {
                                                return [$item->id => "<span class='me-3 text-sm font-medium'>{$item->client->name}</span><br><span class='me-3 text-xs text-gray-400'>{$item->process}</span>"];
                                            })
                                            ->toArray();
                                    })
                                    ->preload()
                                    ->allowHtml(),
                                Forms\Components\Select::make('supplier_id')
                                    ->label('Fornecedor')
                                    ->relationship('supplier', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('employee_id')
                                    ->label('Funcionário')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Forms\Components\Fieldset::make()
                            ->schema([
                                Forms\Components\Textarea::make('note')
                                    ->label('Observação')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Fieldset::make()
                            ->columns(5)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('Forma de pagamento')
                                    ->options(PaymentMethodEnum::class)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Valor')
                                    ->prefix('R$')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Hidden::make('transaction_date')
                                    ->label('Data da transação')
                                    ->default(Carbon::now()),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Data de vencimento'),
                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('Data de pagamento'),
                            ]),

                        Forms\Components\Fieldset::make()
                            ->columns(5)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Ativo?')
                                    ->default(true),

                                Forms\Components\Repeater::make('files')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_path')
                                            ->columnSpanFull()
                                            ->label('Comprovante ou anexo')
                                            ->directory('financial/transactions'),
                                    ])
                            ]),

                        Forms\Components\Hidden::make('modified_by')
                            ->default(auth()->user()->id),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Conta')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Pago em')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->label('Situação')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->color(fn ($record) => $record->type == TransactionTypeEnum::INCOME ? 'success' : 'danger')
                    ->money('BRL')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        // Tables\Columns\Summarizers\Sum::make()
                        //     ->money('BRL'),
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total')
                            ->money('BRL')
                            ->using(function (\Illuminate\Database\Query\Builder $query) {

                                $result = $query->select(

                                    DB::raw("SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_income"),
                                    DB::raw("SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_expense")

                                )->first();

                                return $result->total_income - $result->total_expense;
                            })
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Transação')
                    ->date('d/m/Y')
                    ->toggleable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('financial_account_id')
                    ->label('Conta')
                    ->options(FinancialAccount::where('is_active', true)->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(TransactionTypeEnum::class),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(TransactionStatusEnum::class),


                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Jan 10, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Transações de ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'até ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label('Data da transação')
                    ->date()
                    ->collapsible(),

                Tables\Grouping\Group::make('status')
                    ->label('Status')
                    ->collapsible(),

                Tables\Grouping\Group::make('type')
                    ->label('Tipo')
                    ->collapsible(),
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
            'index' => Pages\ListFinancialTransactions::route('/'),
            'create' => Pages\CreateFinancialTransaction::route('/create'),
            'edit' => Pages\EditFinancialTransaction::route('/{record}/edit'),
        ];
    }
}
