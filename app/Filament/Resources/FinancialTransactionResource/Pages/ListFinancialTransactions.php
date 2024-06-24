<?php

namespace App\Filament\Resources\FinancialTransactionResource\Pages;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\FinancialTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFinancialTransactions extends ListRecords
{
    protected static string $resource = FinancialTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->label('Todos'),

            'icomes' => Tab::make()
                ->label('Receitas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', TransactionTypeEnum::INCOME)),

            'expense' => Tab::make()
                ->label('Despesas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', TransactionTypeEnum::EXPENSE)),

            'transfers' => Tab::make()
                ->label('Transferências')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', TransactionTypeEnum::TRANSFER)),
        ];
    }
}
