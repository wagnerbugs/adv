<?php

namespace App\Filament\Resources;

use Exception;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use App\Models\Client;
use Filament\Forms\Form;
use App\Enums\GenderEnum;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\DocumentTypeEnum;
use App\Enums\MaritalStatusEnum;
use App\Models\ClientIndividual;
use Filament\Resources\Resource;
use App\Enums\EducationLevelEnum;
use Illuminate\Support\HtmlString;
use App\Enums\TreatmentPronounEnum;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Filament\Resources\ClientIndividualResource\Pages;
use App\Filament\Resources\ClientResource\Pages\CreateClient;

class ClientIndividualResource extends Resource
{
    protected static ?string $model = ClientIndividual::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $modelLabel = 'Pessoa Física';

    protected static ?string $pluralModelLabel = 'Pessoa Física';

    protected static ?string $navigationGroup = 'CLIENTES';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

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
                                            ->columns(3)
                                            ->schema([
                                                Forms\Components\Select::make('title')
                                                    ->label('Título')
                                                    ->columnSpan(1)
                                                    ->options(TreatmentPronounEnum::class),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->columnSpan(2)
                                                    ->disabled(),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->email(),

                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Telefone')
                                                    ->mask(RawJs::make(<<<'JS'
                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                    JS)),

                                                Forms\Components\TextInput::make('website')
                                                    ->label('Website/Rede Social')
                                                    ->placeholder('https://www.site.com')
                                                    ->url(),
                                            ]),
                                        Forms\Components\Section::make()
                                            ->columns(4)
                                            ->schema([
                                                Forms\Components\Select::make('gender')
                                                    ->label('Gênero')
                                                    ->options(GenderEnum::class),

                                                Forms\Components\DatePicker::make('birth_date')
                                                    ->label('Data de nascimento'),

                                                Forms\Components\Select::make('marital_status')
                                                    ->label('Estado Civil')
                                                    ->options(MaritalStatusEnum::class),

                                                Forms\Components\Select::make('education_level')
                                                    ->label('Escolaridade')
                                                    ->options(EducationLevelEnum::class),

                                                Forms\Components\TextInput::make('birth_place')
                                                    ->label('Naturalidade')
                                                    ->helperText('Ex: São Paulo - SP')
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('nationality')
                                                    ->label('Nacionalidade')
                                                    ->helperText('Ex: Brasil')
                                                    ->columnSpan(2),
                                            ]),
                                        Forms\Components\Fieldset::make('Filiação')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('mother_name')
                                                    ->label('Nome da mãe'),
                                                Forms\Components\TextInput::make('father_name')
                                                    ->label('Nome do pai'),
                                            ]),

                                    ]),
                                Forms\Components\Tabs\Tab::make('Dados adicionais')
                                    ->schema([

                                        Forms\Components\Fieldset::make('Ocupação')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('workplace')
                                                    ->label('Empresa'),
                                                Forms\Components\TextInput::make('ocupation')
                                                    ->label('Profissão'),
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
                                                        'whatsapp' => 'Whatsapp',
                                                        'residential' => 'Residencial',
                                                        'commercial' => 'Comercial',
                                                        'cellphone' => 'Celular',
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

                                        Forms\Components\Repeater::make('emails')
                                            ->label('E-mails adicionais')
                                            ->columns(3)
                                            ->collapsed()
                                            ->grid(2)
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->label('Tipo')
                                                    ->options([
                                                        'professional' => 'Profissional',
                                                        'particular' => 'Particular',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->email()
                                                    ->required()
                                                    ->columnSpan(2),
                                            ]),

                                        Forms\Components\Repeater::make('websites')
                                            ->label('Websites adicionais')
                                            ->collapsed()
                                            ->grid(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('link')
                                                    ->label('Link')
                                                    ->helperText('Ex: https://google.com')
                                                    ->url()
                                                    ->required(),
                                            ]),

                                        Forms\Components\Repeater::make('documents')
                                            ->label('Documentos')
                                            ->columns(2)
                                            ->collapsed()
                                            ->grid(2)
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
                                Forms\Components\Tabs\Tab::make('Endereço')
                                    ->schema([
                                        Forms\Components\Group::make()
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

                                                Forms\Components\Repeater::make('addresses')
                                                    ->label('Outros endereços')
                                                    ->columnSpanFull()
                                                    ->collapsed()
                                                    ->addActionLabel('Registrar outros endereços relevantes')
                                                    ->schema([
                                                        Forms\Components\Group::make()
                                                            ->columns(4)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('zipcode')
                                                                    ->label('CEP')
                                                                    ->columnSpan(1)
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
                                                                Forms\Components\TextInput::make('street')
                                                                    ->label('Rua, Via, Avenida...')
                                                                    ->columnSpan(2),
                                                                Forms\Components\TextInput::make('number')
                                                                    ->label('Número')
                                                                    ->columnSpan(1),
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
                                    ]),

                                Forms\Components\Tabs\Tab::make('Dados bancários')
                                    ->schema([

                                        Forms\Components\Fieldset::make('Conta bancária')
                                            ->columns(4)
                                            ->schema([
                                                Forms\Components\Select::make('type_account_bank')
                                                    ->label('Tipo de conta')
                                                    ->columnSpan(1)
                                                    ->options(TypeOfBankAccountEnum::class),
                                                Forms\Components\Select::make('bank_name')
                                                    ->label('Banco')
                                                    ->columnSpan(3)
                                                    ->options(Bank::all()->map(function ($bank) {
                                                        return strtoupper($bank->compe . ' - ' . $bank->long_name);
                                                    }))
                                                    ->searchable(),
                                                Forms\Components\TextInput::make('bank_agency')
                                                    ->label('Agência')
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('bank_account')
                                                    ->label('Conta')
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('pix')
                                                    ->label('PIX')
                                                    ->columnSpanFull(),
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
                                                        'whatsapp' => 'Whatsapp',
                                                        'residential' => 'Residencial',
                                                        'commercial' => 'Comercial',
                                                        'cellphone' => 'Celular',
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

                                        Forms\Components\Repeater::make('emails')
                                            ->label('E-mails adicionais')
                                            ->columns(3)
                                            ->collapsed()
                                            ->grid(2)
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->label('Tipo')
                                                    ->options([
                                                        'professional' => 'Profissional',
                                                        'particular' => 'Particular',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->email()
                                                    ->required()
                                                    ->columnSpan(2),
                                            ]),

                                        Forms\Components\Repeater::make('websites')
                                            ->label('Websites adicionais')
                                            ->collapsed()
                                            ->grid(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('link')
                                                    ->label('Link')
                                                    ->helperText('Ex: https://google.com')
                                                    ->url()
                                                    ->required(),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arquivos')
                                    ->schema([

                                        Forms\Components\Placeholder::make('attachments_placeholder')
                                            ->label('Anexos')
                                            ->content(
                                                function (ClientIndividual $record): HtmlString {
                                                    $files = $record->attachments;
                                                    if ($files) {
                                                        $filesList = '';
                                                        foreach ($files as $note) {

                                                            $filesList .= $note['title'] . ' - <a class="text-violet-500 hover:text-violet-600" href="' . Storage::url($note['file']) . '" target="_blank">' . $note['file'] . '</a></br>';

                                                            return new HtmlString($filesList);
                                                        }
                                                    }
                                                    return new HtmlString('Nenhum arquivo anexado');
                                                }
                                            ),

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

                                        Forms\Components\Placeholder::make('annotation_placeholder')
                                            ->label('Anotações')
                                            ->content(
                                                function (ClientIndividual $record): HtmlString {
                                                    $notes = $record->annotations;
                                                    if ($notes) {
                                                        $noteList = '';
                                                        foreach ($notes as $note) {

                                                            $noteList .= Carbon::parse($note['date']) . ' - ' . $note['author'] . ' - <span class="text-violet-500">' . $note['annotation'] . '</span></br>';

                                                            return new HtmlString($noteList);
                                                        }
                                                    }
                                                    return new HtmlString('Nenhuma anotação registrada');
                                                }
                                            ),

                                        Forms\Components\Repeater::make('annotations')
                                            ->label('Anotações')
                                            ->columns(2)
                                            ->collapsed()
                                            ->deletable(false)
                                            ->addActionLabel('Adicionar anotação')
                                            ->schema([
                                                Forms\Components\Hidden::make('date')
                                                    ->label('Data')
                                                    ->default(Carbon::now()),
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

                Forms\Components\Group::make()
                    ->columns(1)
                    ->schema([

                        Forms\Components\Section::make()
                            ->schema([

                                Forms\Components\FileUpload::make('image')
                                    ->label('Foto')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->directory('clients'),

                                Forms\Components\Fieldset::make('Status')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Ativo')
                                            ->default(true),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image')
                    ->label('Imagem'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientIndividual $record): string => $record->client->document),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->icon('heroicon-m-phone')
                    ->iconColor('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->icon('heroicon-m-at-symbol')
                    ->iconColor('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable(),

                Tables\Columns\TextColumn::make('state')
                    ->label('UF')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo?'),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListClientIndividuals::route('/'),
            // 'create' => Pages\CreateClientIndividual::route('/create'),
            'create' => CreateClient::route('/create'),
            'edit' => Pages\EditClientIndividual::route('/{record}/edit'),
        ];
    }
}
