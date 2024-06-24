<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\FinancialCategory;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FinancialCategoryResource\Pages;

class FinancialCategoryResource extends Resource
{
    protected static ?string $model = FinancialCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'FINANCEIRO';

    protected static ?string $modelLabel = 'Categoria';

    protected static ?string $pluralModelLabel = 'Categorias';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Forms\Components\Section::make()
                    ->columnSpan(3)
                    ->columns(3)
                    ->schema([

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->searchable()
                            ->options(TransactionTypeEnum::class)
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nome da categoria')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('parent_id')
                            ->label('Categoria Pai')
                            // ->relationship(FinancialCategory::getParentCategories())
                            ->options(FinancialCategory::getParentCategories())
                            ->preload()
                            ->searchable()
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Caso seja categoria pai, não selecione um opção'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),

                    ]),

                Forms\Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo?')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Subcategorias')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Categorias')
                    ->sortable()
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de trançações')
                    ->options(TransactionTypeEnum::class),

                Tables\Filters\SelectFilter::make('parent_category')
                    ->label('Categoria Pai')
                    ->options(FinancialCategory::getParentCategories())
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where('parent_id', $data['value'])
                                ->orWhere('id', $data['value']);
                        }
                    }),
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
            'index' => Pages\ListFinancialCategories::route('/'),
            'create' => Pages\CreateFinancialCategory::route('/create'),
            'edit' => Pages\EditFinancialCategory::route('/{record}/edit'),
        ];
    }
}
