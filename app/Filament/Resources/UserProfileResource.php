<?php

namespace App\Filament\Resources;

use Exception;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\GenderEnum;
use App\Models\Ocupation;
use App\Helpers\CboHelper;
use Filament\Tables\Table;
use App\Models\UserProfile;
use Filament\Support\RawJs;
use Illuminate\Support\Str;
use App\Enums\DocumentTypeEnum;
use App\Models\OcupationFamily;
use App\Enums\MaritalStatusEnum;
use Filament\Resources\Resource;
use App\Enums\EducationLevelEnum;
use App\Enums\EmploymentTypeEnum;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use App\Filament\Resources\UserProfileResource\Pages;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $modelLabel = 'Profissional';

    protected static ?string $pluralModelLabel = 'Profissionais';

    protected static ?string $navigationGroup = 'RECURSOS HUMANOS';

    protected static ?int $navigationSort = 6;

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
                                Forms\Components\Tabs\Tab::make('Dados pessoais')
                                    ->schema([
                                        Forms\Components\Section::make()
                                            ->relationship('user')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->disabled(),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->disabled(),
                                            ]),
                                        Forms\Components\Section::make()
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
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('hire_date')
                                                    ->label('Data de admissão'),
                                                Forms\Components\DatePicker::make('termination_date')
                                                    ->label('Data da demissão'),
                                            ]),

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
                                                                        $response = Http::get('https://brasilapi.com.br/api/cep/v2/' . $state);
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
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('contract')
                                            ->label('Número do Contrato')
                                            ->maxLength(255),
                                        Forms\Components\Select::make('employment_type')
                                            ->label('Tipo de Contrato')
                                            ->options(EmploymentTypeEnum::class),

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
                                                        Forms\Components\Select::make('ocupation')
                                                            ->label('Ocupação CBO')
                                                            ->columnSpanFull()
                                                            ->live('onBlur', true)
                                                            ->options(Ocupation::all()->mapWithKeys(function ($ocupation) {
                                                                return [$ocupation->code => $ocupation->code . ' - ' . $ocupation->description];
                                                            }))
                                                            ->searchable()
                                                            ->required()
                                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                                $code = CboHelper::cboCode($state);
                                                                $set('code', $code);

                                                                $codeFamily = CboHelper::convertToFamilyCodeCbo($state);
                                                                $family = OcupationFamily::where('code', $codeFamily)->first();
                                                                $set('family', $family->description);
                                                            }),
                                                    ]),

                                            ]),

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
                                Forms\Components\Tabs\Tab::make('Arquivos')
                                    ->schema([
                                        Forms\Components\Repeater::make('attachments')
                                            ->label('Arquivos')
                                            ->collapsed()
                                            ->grid(2)
                                            ->addActionLabel('Anexar arquivo')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Título do arquivo')
                                                    ->placeholder('Descrição curta do documento')
                                                    ->maxLength(255),
                                                Forms\Components\FileUpload::make('file')
                                                    ->label('Arquivo')
                                                    ->directory('employees'),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Anotações')
                                    ->schema([
                                        Forms\Components\Repeater::make('annotations')
                                            ->label('Anotações')
                                            ->columns(2)
                                            ->collapsed()
                                            ->deletable(false)
                                            ->addActionLabel('Adicionar anotação')
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('date')
                                                    ->label('Data')
                                                    ->default(now())
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make('author')
                                                    ->label('Autor')
                                                    ->default(auth()->user()->name)
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make('annotation')
                                                    ->label('Nota')
                                                    // ->disabled(!auth()->user()->hasRole('Root'))
                                                    ->required()
                                                    ->placeholder('Anotação...')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Dicas')

                                    ->schema([

                                        Forms\Components\Placeholder::make('')
                                            ->content(new HtmlString('
                                                        <h1 class="font-bold pb-2">CBOs mais comuns</h1>
                                                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                                    <tr>
                                                                        <th scope="col" class="px-6 py-3">
                                                                            Ocupação
                                                                        </th>
                                                                        <th scope="col" class="px-6 py-3">
                                                                            Família
                                                                        </th>
                                                                        <th scope="col" class="px-6 py-3">
                                                                            Código
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Advogado
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-05
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Advogado de empresa
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-10
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="bg-white dark:bg-gray-800">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Advogado (Direito Civil)
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-15
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="bg-white dark:bg-gray-800">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Advogado (Direito Médio)
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-20
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="bg-white dark:bg-gray-800">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Advogado (Direito Penal)
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-25
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="bg-white dark:bg-gray-800">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Advogado (Áreas Especiais)
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-30
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="bg-white dark:bg-gray-800">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                        Advogado (Direito do Trabalho)
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-35
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="bg-white dark:bg-gray-800">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Consultor Jurídico
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            2410 - Advogado
                                                                        </td>
                                                                        <td class="px-6 py-4">
                                                                            2410-40
                                                                        </td>
                                                                    </tr>
                                                            </tbody>
                                                        </table>

                                                    ')),

                                    ]),

                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->columns(1)
                    ->schema([

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

                        Forms\Components\Repeater::make('documents')
                            ->label('Documentos')
                            ->columns(2)
                            ->collapsed()
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
                Tables\Columns\ViewColumn::make('cbos')
                    ->label('Cargo')
                    ->view('tables.columns.cbo-data')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_lawyer')
                    ->label('Advogado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo')
                    ->searchable()
                    ->sortable(),
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
