<?php

namespace App\Filament\Resources;

use App\Enums\DocumentTypeEnum;
use App\Enums\EducationLevelEnum;
use App\Enums\EmploymentTypeEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Helpers\CboHelper;
use App\Models\Occupation;
use App\Models\OccupationFamily;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Profissional';

    protected static ?string $pluralModelLabel = 'Profissionais';

    protected static ?string $navigationGroup = 'CADASTROS';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([
                Forms\Components\Section::make('Informação pessoal')
                    ->visibleOn('create')
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->hiddenOn('edit'),

                        Forms\Components\Select::make('roles')
                            ->label('Nome da Regra')
                            ->multiple()
                            ->preload()
                            ->relationship('roles', 'name', fn (Builder $query) => auth()->user()->hasRole('Root') ? null : $query->where('id', '>=', auth()->user()->roles()->first()->id)),
                    ]),

                Forms\Components\Group::make()
                    ->visibleOn('edit')
                    ->columnSpan(4)
                    ->schema([

                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Dados pessoais')
                                    ->schema([
                                        Forms\Components\Section::make()
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->disabled()
                                                    ->dehydrated(),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->email()
                                                    ->disabled()
                                                    ->dehydrated(),
                                            ]),
                                        Forms\Components\Section::make()
                                            ->relationship('profile')
                                            ->columns(4)
                                            ->schema([
                                                Forms\Components\Select::make('gender')
                                                    ->label('Genero')
                                                    ->options(GenderEnum::class),

                                                Forms\Components\DatePicker::make('birth_date')
                                                    ->label('Data de nascimento'),

                                                Forms\Components\Select::make('marital_status')
                                                    ->label('Estado Civil')
                                                    ->options(MaritalStatusEnum::class),

                                                Forms\Components\Select::make('education_level')
                                                    ->label('Escolaridade')
                                                    ->options(EducationLevelEnum::class),
                                            ]),
                                        Forms\Components\Section::make()
                                            ->relationship('profile')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('hire_date')
                                                    ->label('Data de admissão'),
                                                Forms\Components\DatePicker::make('termination_date')
                                                    ->label('Data da demissão'),
                                            ]),

                                        Forms\Components\Section::make()
                                            ->relationship('profile')
                                            ->schema([

                                                Forms\Components\Repeater::make('phones')
                                                    ->label('Telefones')
                                                    ->columns(3)
                                                    ->collapsed()
                                                    ->grid(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('type')
                                                            ->label('Tipo')
                                                            ->options([
                                                                'Residencial' => 'Residencial',
                                                                'Comercial' => 'Comercial',
                                                                'Celular' => 'Celular',
                                                            ])
                                                            ->required()
                                                            ->columnSpan(1),
                                                        Forms\Components\TextInput::make('number')
                                                            ->label('Número')
                                                            ->mask(RawJs::make(<<<'JS'
                                                            $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                        JS))
                                                            ->required()
                                                            ->columnSpan(2),
                                                    ]),

                                                Forms\Components\Repeater::make('documents')
                                                    ->label('Documentos')
                                                    ->columns(2)
                                                    ->collapsed()
                                                    ->grid(3)
                                                    ->addActionLabel('Adicionar novo documento')
                                                    ->schema([
                                                        Forms\Components\Select::make('type')
                                                            ->label('Tipo')
                                                            ->options(DocumentTypeEnum::class)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('number')
                                                            ->label('Número')
                                                            ->placeholder('1234567890')
                                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'No caso de OAB, informe o número e a UF. Ex: 12345-SP')
                                                            ->required(),
                                                    ]),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Endereço')
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship('address')
                                            ->columns(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('zipcode')
                                                    ->label('CEP')
                                                    ->mask('99999-999')
                                                    ->suffixAction(
                                                        fn ($state, $set) => Forms\Components\Actions\Action::make('Buscar CEP')
                                                            ->icon('heroicon-m-globe-alt')
                                                            ->action(
                                                                function () use ($state, $set) {
                                                                    $state = preg_replace('/[^0-9]/', '', $state);
                                                                    if (strlen($state) != 8) {
                                                                        Notification::make()
                                                                            ->danger()
                                                                            ->title('Digite um cep valido')
                                                                            ->send();
                                                                    }

                                                                    try {
                                                                        $response = Http::get('https://brasilapi.com.br/api/cep/v2/'.$state);
                                                                        $data = $response->json();

                                                                        $set('street', $data['street']);
                                                                        $set('neighborhood', $data['neighborhood']);
                                                                        $set('city', $data['city']);
                                                                        $set('state', $data['state']);
                                                                        if (isset($data['location']['coordinates']['longitude'])) {
                                                                            $set('longitude', $data['location']['coordinates']['longitude']);
                                                                            $set('latitude', $data['location']['coordinates']['latitude']);
                                                                        }
                                                                    } catch (Exception $e) {
                                                                        Notification::make()
                                                                            ->danger()
                                                                            ->title($e->getMessage())
                                                                            ->send();
                                                                    }
                                                                }
                                                            )
                                                    ),

                                                Forms\Components\Fieldset::make('Endereço')
                                                    ->columnSpan(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('street')
                                                            ->label('Rua, Via, Avenida...'),
                                                        Forms\Components\TextInput::make('number')
                                                            ->label('Número'),
                                                        Forms\Components\TextInput::make('complement')
                                                            ->label('Complemento'),
                                                        Forms\Components\TextInput::make('neighborhood')
                                                            ->label('Bairro'),
                                                        Forms\Components\TextInput::make('city')
                                                            ->label('Cidade')
                                                            ->disabled()
                                                            ->dehydrated(),
                                                        Forms\Components\TextInput::make('state')
                                                            ->label('UF')
                                                            ->disabled()
                                                            ->dehydrated(),
                                                        Forms\Components\TextInput::make('longitude')
                                                            ->label('Longitude'),
                                                        Forms\Components\TextInput::make('latitude')
                                                            ->label('Latitude'),
                                                    ]),

                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Contrato')
                                    // ->columns(2)
                                    ->schema([

                                        Forms\Components\Group::make()
                                            ->relationship('profile')
                                            ->schema([

                                                Forms\Components\Section::make()
                                                    ->columns(2)
                                                    ->schema([

                                                        Forms\Components\TextInput::make('contract')
                                                            ->label('Número do Contrato')
                                                            ->maxLength(255),
                                                        Forms\Components\Select::make('employment_type')
                                                            ->label('Tipo de Contrato')
                                                            ->options(EmploymentTypeEnum::class),
                                                    ]),

                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\Repeater::make('cbos')
                                                            ->label('Registro de ocupações')
                                                            ->columnSpanFull()
                                                            ->collapsed()
                                                            ->addActionLabel('Registrar mudança de ocupação')
                                                            ->schema([
                                                                Forms\Components\Group::make()
                                                                    ->columns(4)
                                                                    ->schema([
                                                                        Forms\Components\DatePicker::make('date')
                                                                            ->label('Data')
                                                                            ->columnSpan(1)
                                                                            ->default(Carbon::now())
                                                                            ->required(),
                                                                        Forms\Components\TextInput::make('code')
                                                                            ->label('Código CBO')
                                                                            ->columnSpan(1)
                                                                            ->disabled()
                                                                            ->dehydrated(),
                                                                        Forms\Components\TextInput::make('family')
                                                                            ->label('Família CBO')
                                                                            ->columnSpan(2)
                                                                            ->disabled()
                                                                            ->dehydrated(),
                                                                        Forms\Components\TextInput::make('occupation')
                                                                            ->label('Família CBO')
                                                                            ->columnSpan(2)
                                                                            ->hidden(),

                                                                        Forms\Components\Select::make('occupation_search')
                                                                            ->label('Ocupação CBO')
                                                                            ->columnSpanFull()
                                                                            ->live('onBlur', true)
                                                                            ->options(Occupation::where('is_active', true)->get()->mapWithKeys(function ($occupation) {
                                                                                return [$occupation->code => $occupation->code.' - '.$occupation->description];
                                                                            }))
                                                                            ->searchable()
                                                                            ->required()
                                                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                                                $code = CboHelper::cboCode($state);
                                                                                $set('code', $code);

                                                                                $codeFamily = CboHelper::convertToFamilyCodeCbo($state);

                                                                                $family = OccupationFamily::where('code', $codeFamily)->first();
                                                                                $set('family', $family->description);

                                                                                $codeOccupation = Occupation::where('code', $state)->first();
                                                                                $set('occupation', $codeOccupation->description);
                                                                            }),
                                                                    ]),

                                                            ]),
                                                    ]),

                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\Repeater::make('salaries')
                                                            ->label('Registro de salários')
                                                            ->columnSpanFull()
                                                            ->collapsed()
                                                            ->grid(2)
                                                            ->addActionLabel('Registrar novo salário')
                                                            ->schema([
                                                                Forms\Components\Group::make()
                                                                    ->columns(2)
                                                                    ->schema([
                                                                        Forms\Components\DatePicker::make('date')
                                                                            ->label('Data')
                                                                            ->required(),
                                                                        Forms\Components\TextInput::make('amount')
                                                                            ->label('Quantia')
                                                                            ->numeric()
                                                                            ->rule('regex: /^\d+(\.\d{1,2})?$/')
                                                                            ->required(),
                                                                    ]),

                                                            ]),
                                                    ]),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arquivos')
                                    ->schema([

                                        Forms\Components\Placeholder::make('attachmentsList')
                                            ->label('Lista de Anexos')
                                            ->live()
                                            ->content(
                                                function (User $record): HtmlString {
                                                    $notes = $record->attachments;

                                                    if ($notes) {
                                                        $noteList = '';
                                                        foreach ($notes as $note) {
                                                            $noteList .= '<a href="/storage/'.$note->path.'" class="text-violet-500 hover:text-violet-600" target="_blank">'.Carbon::parse($note->created_at)->format('d/m/Y').' - '.$note->name.' - '.$note->path.'</a><br/>';
                                                        }

                                                        return new HtmlString($noteList);
                                                    }

                                                    return new HtmlString('Sem anexos');
                                                }
                                            ),

                                        Forms\Components\Repeater::make('attachments')
                                            ->label('Arquivos')
                                            ->relationship('attachments')
                                            ->collapsed()
                                            ->grid(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Título do arquivo')
                                                    ->placeholder('Descrição curta do documento')
                                                    ->maxLength(255),
                                                Forms\Components\FileUpload::make('path')
                                                    ->label('Arquivo')
                                                    ->openable()
                                                    ->downloadable()
                                                    ->previewable()
                                                    ->maxSize('5120')
                                                    ->directory('employees'),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Anotações')
                                    ->schema([
                                        Forms\Components\Section::make()
                                            ->relationship('profile')
                                            ->schema([

                                                Forms\Components\Placeholder::make('annotationsList')
                                                    ->label('Anotações')
                                                    ->live()
                                                    ->content(
                                                        function (UserProfile $record): HtmlString {
                                                            $notes = $record->annotations;

                                                            if ($notes) {
                                                                $noteList = '';
                                                                foreach (array_reverse($notes) as $note) {
                                                                    $noteList .= Carbon::parse($note['date'])->format('d/m/Y').' - '.$note['author'].' - '.$note['annotation'].'<br/>';
                                                                }

                                                                return new HtmlString($noteList);
                                                            }

                                                            return new HtmlString('Sem anotações');
                                                        }
                                                    ),

                                                Forms\Components\Repeater::make('annotations')
                                                    ->label('Anotações')
                                                    ->columns(2)
                                                    ->collapsed()
                                                    ->addActionLabel('Adicionar anotação')
                                                    ->schema([
                                                        Forms\Components\Hidden::make('date')
                                                            ->label('Data')
                                                            ->default(Carbon::now()->format('Y-m-d H:i')),
                                                        Forms\Components\Hidden::make('author')
                                                            ->label('Autor')
                                                            ->default(auth()->user()->name),
                                                        Forms\Components\Textarea::make('annotation')
                                                            ->label('Nota')
                                                            ->required()
                                                            ->placeholder('Anotação...')
                                                            ->columnSpanFull(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                    ]),
                Forms\Components\Group::make()
                    ->visibleOn('edit')

                    ->columns(1)
                    ->schema([

                        Forms\Components\Section::make()
                            ->relationship('profile')
                            ->schema([

                                Forms\Components\Fieldset::make('Avatar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('avatar')
                                            ->label('')
                                            ->columnSpanFull()
                                            ->avatar()
                                            ->directory('professionals'),
                                    ]),

                                Forms\Components\Fieldset::make('Status')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Ativo')
                                            ->default(true),
                                    ]),

                                Forms\Components\Fieldset::make('People Keys')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_lawyer')
                                            ->label('Advogado'),
                                    ]),
                            ]),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('roles')
                                    ->label('Funções')
                                    ->multiple()
                                    ->preload()
                                    ->relationship('roles', 'name', fn (Builder $query) => auth()->user()->hasRole('Root') ? null : $query->where('id', '>=', auth()->user()->roles()->first()->id)),

                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\ImageColumn::make('profile.avatar')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Completo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Tipo de usuário')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ViewColumn::make('profile.cbos')
                    ->label('Cargo')
                    ->view('tables.columns.cbo-data')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('profile.is_lawyer')
                    ->label('Advogado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('profile.is_active')
                    ->label('Ativo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('Data de Admissão')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('Root')) {
            return parent::getEloquentQuery();
        } else {
            return parent::getEloquentQuery()->whereHas(
                'roles',
                fn (Builder $query) => $query->where('id', '>=', auth()->user()->roles()->first()->id)
            );
        }
    }
}
