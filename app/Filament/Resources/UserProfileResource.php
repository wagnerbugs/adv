<?php

namespace App\Filament\Resources;

use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\UserProfileResource\Pages;
use App\Filament\Resources\UserProfileResource\RelationManagers;
use App\Models\UserProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $modelLabel = 'Colaboradores';

    protected static ?string $pluralModelLabel = 'Colaboradores';

    protected static ?string $navigationGroup = 'RECURSOS HUMANOS';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpan(2)
                    ->schema([


                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Tab 1')
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->label('Colaborador')
                                            ->relationship('user', 'name')
                                            ->required(),

                                        Forms\Components\TextInput::make('gender')
                                            ->label('Genero'),
                                        Forms\Components\DatePicker::make('birth_date')
                                            ->label('Data de nascimento'),
                                        Forms\Components\TextInput::make('marital_status')
                                            ->label('Estado Civil'),
                                        Forms\Components\TextInput::make('education_level')
                                            ->label('Escolaridade'),
                                        Forms\Components\DatePicker::make('hire_date')
                                            ->label('Data de admissão'),
                                        Forms\Components\DatePicker::make('termination_date')
                                            ->label('Data da demissão'),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Telefone')
                                            ->tel(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Tab 2')
                                    ->schema([
                                        Forms\Components\TextInput::make('contract')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('employment_type'),
                                        Forms\Components\TextInput::make('cbo_code')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('cbo_title')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('cbo_description')
                                            ->maxLength(255),


                                    ]),
                                Forms\Components\Tabs\Tab::make('Tab 3')
                                    ->schema([
                                        Forms\Components\TextInput::make('attachments'),
                                    ]),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->columns(1)
                    ->schema([
                        Forms\Components\Section::make('Informação pessoal')
                            ->schema([
                                Forms\Components\Repeater::make('documents')
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->options(DocumentTypeEnum::class)
                                            ->required(),
                                        Forms\Components\TextInput::make('number')
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nome Completo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.roles.name')
                    ->label('Tipo de usuário')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Tipo de Contrato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cbo_code')
                    ->label('Código CBO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cbo_title')
                    ->label('Titulo CBO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('Data de Admissão')
                    ->date()
                    ->sortable(),
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
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }
}
