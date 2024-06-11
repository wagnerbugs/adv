<?php

namespace App\Filament\Resources\ClientCompanyResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Pages\EditProcess;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProcessResource;
use App\Models\Process;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProcessesRelationManager extends RelationManager
{
    protected static string $relationship = 'processes';

    protected static ?string $recordTitleAttribute = 'process';

    protected static ?string $title = 'Processos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('process')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('process')
            ->columns([
                Tables\Columns\TextColumn::make('process')
                    ->label('Processo'),

                Tables\Columns\ViewColumn::make('details.professionals')
                    ->label('Profissionais')
                    ->view('tables.columns.process-detail-professionals'),

                Tables\Columns\TextColumn::make('details.class_name')
                    ->label('Classe processual')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('details.judging_name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('details.publish_date')
                    ->label('Data de publicação')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('details.last_modification_date')
                    ->label('Data de modificação')
                    ->badge()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Action::make('Editar')
                    ->url(function (Process $record): string {
                        return route('filament.admin.resources.processes.edit', $record->id);
                    })
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            // 'index' => Pages\ListCustomers::route('/'),
            // 'create' => \App\Filament\Resources\ProcessResource\Pages\CreateProcess::route('/create'),
            // 'view' => Pages\ViewCustomer::route('/{record}'),
            // 'edit' => \App\Filament\Resources\ProcessResource\Pages\EditProcess::route('/{record}/edit'),
            // 'addresses' => Pages\ManageCustomerAddresses::route('/{record}/addresses'),
        ];
    }
}
