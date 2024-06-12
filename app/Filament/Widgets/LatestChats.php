<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Process;
use Filament\Tables\Table;
use App\Models\ProcessChat;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProcessResource;
use App\Filament\Resources\ProcessChatResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestChats extends BaseWidget
{
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {


        return $table
            ->query(ProcessChatResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile.avatar')
                    ->label('Usuário')
                    ->circular()
                    ->ring(5),

                Tables\Columns\TextColumn::make('message')
                    ->label('Processo')
                    ->description(
                        function (ProcessChat $record) {

                            return $record->process->process;
                        }
                    )
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query
                                ->with(['user', 'process'])
                                ->where(function ($query) use ($search) {
                                    $query->whereHas('user', function ($query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%");
                                    })
                                        ->orWhereHas('process', function ($query) use ($search) {
                                            $query->where('process', 'like', "%{$search}%");
                                        });
                                });
                        }
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->sortable()
                    ->dateTime(),


            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Action::make('Editar')
                    ->url(function (ProcessChat $record): string {
                        return route('filament.admin.resources.processes.edit', $record->process_id);
                    })
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->poll('10s')
            ->defaultSort('created_at', 'desc');
    }

    public static function canView(): bool
    {
        return  auth()->user()->hasPermissionTo('view_any_widgets');
    }
}
