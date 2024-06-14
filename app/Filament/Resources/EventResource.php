<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Event;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventResource\RelationManagers;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'teste';

    protected static ?string $pluralModelLabel = 'teste';

    protected static bool $isDiscovered = true;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([

                        Forms\Components\Hidden::make('user_id')
                            ->label('Usuário')
                            ->default(auth()->user()->id)
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\ColorPicker::make('color')
                            ->label('Cor'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('professionals')
                            ->label('Profissionais')
                            ->options(
                                User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                                    ->where('user_profiles.is_active', true)
                                    ->pluck('users.name', 'users.id')
                                    ->toArray()
                            )
                            ->multiple()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Início')
                            ->required(),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fim')
                            ->required(),

                        Forms\Components\Toggle::make('is_audience')
                            ->label('É audiência?')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_audience')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
            'calendar' => Pages\FullCalendar::route('/calendar'),
        ];
    }
}
