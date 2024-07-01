<?php

namespace App\Filament\Resources;

use stdClass;
use Exception;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use App\Models\Client;
use App\Models\Process;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Enums\GenderEnum;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\ClientTypeEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\ClientIndividual;
use App\Models\FinancialPayment;
use Filament\Resources\Resource;
use App\Enums\EducationLevelEnum;
use Illuminate\Support\HtmlString;
use App\Enums\TreatmentPronounEnum;
use Filament\Tables\Actions\Action;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Leandrocfe\FilamentPtbrFormFields\Money;
use App\Filament\Resources\ClientResource\Pages;
use App\Tables\Columns\Oie;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'CLIENTES';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public Client $teste;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['document', 'company.company', 'individual.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Cliente' => $record->company->company ?? $record->individual->name,
            'Telefone' => $record->company->phone ?? $record->individual->phone,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        if ($record->type == ClientTypeEnum::COMPANY) {
            return ClientCompanyResource::getUrl('edit', ['record' => $record]);
        }

        if ($record->type == ClientTypeEnum::INDIVIDUAL) {
            return ClientIndividualResource::getUrl('edit', ['record' => $record]);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->visibleOn('create')
                    ->columns(4)
                    ->schema([

                        Forms\Components\Section::make()
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo de cliente')
                                    ->options(ClientTypeEnum::class)
                                    ->required(),

                                Forms\Components\TextInput::make('document')
                                    ->label('Documento')
                                    ->unique(table: Client::class)
                                    ->required()
                                    ->mask(RawJs::make(<<<'JS'
                                        $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                                    JS))
                                    ->rule('cpf_ou_cnpj'),
                            ]),
                    ]),

                Forms\Components\Grid::make()
                    ->visibleOn('edit')
                    ->columns(4)
                    ->schema(fn (Get $get): array => match ((int)$get('type')) {
                        1 => [
                            Forms\Components\Group::make()
                                ->columnSpan(3)
                                ->schema([
                                    Forms\Components\Tabs::make('Tabs')
                                        ->tabs([

                                            Forms\Components\Tabs\Tab::make('Dados pessoais')
                                                ->schema([

                                                    Forms\Components\Section::make()
                                                        ->relationship('individual')
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
                                                                ->mask(
                                                                    RawJs::make(<<<'JS'
                                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                                    JS)
                                                                ),

                                                            Forms\Components\TextInput::make('website')
                                                                ->label('Website/Rede Social')
                                                                ->placeholder('https://www.site.com')
                                                                ->url(),
                                                        ]),
                                                    Forms\Components\Section::make()
                                                        ->relationship('individual')
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
                                                        ->relationship('individual')
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
                                                        ->relationship('individual')
                                                        ->columns(2)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('workplace')
                                                                ->label('Empresa'),
                                                            Forms\Components\TextInput::make('ocupation')
                                                                ->label('Profissão'),
                                                        ]),

                                                    Forms\Components\Section::make()
                                                        ->relationship('individual')
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
                                                ]),
                                            Forms\Components\Tabs\Tab::make('Endereço')
                                                ->schema([
                                                    Forms\Components\Group::make()
                                                        ->relationship('individual')
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
                                                        ->relationship('individual')
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
                                                                ->multiple()
                                                                ->openable()
                                                                ->downloadable()
                                                                ->previewable()
                                                                ->maxSize('5120')
                                                                ->directory('clients/files'),
                                                        ]),
                                                ]),

                                            Forms\Components\Tabs\Tab::make('Anotações')
                                                ->schema([

                                                    Forms\Components\Repeater::make('notes')
                                                        ->label('Anotações')
                                                        ->collapsed()
                                                        ->deletable(false)
                                                        ->addActionLabel('Adicionar anotação')
                                                        ->schema([
                                                            Forms\Components\Hidden::make('user_id')
                                                                ->label('Autor')
                                                                ->default(auth()->user()->id),
                                                            Forms\Components\RichEditor::make('note')
                                                                ->label('Nota')
                                                                ->required()
                                                                ->placeholder('Anotação...')
                                                                ->columnSpanFull(),
                                                        ]),
                                                ]),
                                        ]),

                                ]),
                            Forms\Components\Group::make()
                                ->relationship('individual')
                                ->columns(1)
                                ->schema([

                                    Forms\Components\Section::make()
                                        ->schema([

                                            Forms\Components\Fieldset::make('Foto')
                                                ->schema([

                                                    Forms\Components\FileUpload::make('image')
                                                        ->label('')
                                                        ->avatar()
                                                        ->directory('clients/images'),
                                                ]),

                                            Forms\Components\Fieldset::make('Status')
                                                ->schema([
                                                    Forms\Components\Toggle::make('is_active')
                                                        ->label('Ativo')
                                                        ->default(true),
                                                ]),
                                        ]),
                                ]),
                        ],
                        2 => [
                            Forms\Components\Group::make()
                                ->columnSpan(3)
                                ->schema([
                                    Forms\Components\Tabs::make('Tabs')
                                        ->contained(true)
                                        ->tabs([
                                            Forms\Components\Tabs\Tab::make('Empresa')
                                                ->schema([
                                                    Forms\Components\Section::make()
                                                        ->relationship('company')
                                                        ->columns(6)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('company')
                                                                ->label('Empresa')
                                                                ->columnSpan(3)
                                                                ->disabled(),

                                                            Forms\Components\TextInput::make('fantasy_name')
                                                                ->label('Nome Fantasia')
                                                                ->columnSpan(3)
                                                                ->disabled(),

                                                            Forms\Components\TextInput::make('email')
                                                                ->label('E-mail')
                                                                ->columnSpan(2)
                                                                ->email(),

                                                            Forms\Components\TextInput::make('phone')
                                                                ->label('Telefone')
                                                                ->columnSpan(2)
                                                                ->mask(
                                                                    RawJs::make(
                                                                        <<<'JS'
                                                                            $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                                        JS,
                                                                    ),
                                                                ),

                                                            Forms\Components\TextInput::make('website')
                                                                ->label('Website/Rede Social')
                                                                ->columnSpan(2)
                                                                ->placeholder('https://www.site.com')
                                                                ->url(),

                                                            Forms\Components\TextInput::make('company_size')
                                                                ->label('Porte da empresa')
                                                                ->columnSpan(2),

                                                            Forms\Components\TextInput::make('legal_nature')
                                                                ->label('Natureza Jurídica')
                                                                ->columnSpan(2),

                                                            Forms\Components\TextInput::make('type')
                                                                ->label('Tipo')
                                                                ->columnSpan(2),

                                                            Money::make('share_capital')
                                                                ->label('Capital social')
                                                                ->columnSpan(2),


                                                        ]),


                                                ]),

                                            Forms\Components\Tabs\Tab::make('CNPJ')
                                                ->schema([

                                                    Forms\Components\Section::make()
                                                        ->relationship('company')
                                                        ->schema([

                                                            Forms\Components\Fieldset::make('Estabelecimento')
                                                                ->columns(2)
                                                                ->schema([Forms\Components\TextInput::make('registration_status')->label('Situação Cadastral'), Forms\Components\DatePicker::make('registration_date')->label('Data de Cadastramento'), Forms\Components\DatePicker::make('activity_start_date')->label('Data de Início de Atividade'), Forms\Components\TextInput::make('main_activity')->label('Atividade primária')]),

                                                            Forms\Components\Fieldset::make('Inscrições Estadual')
                                                                ->columns(1)
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('state_registrations')
                                                                        ->label('')
                                                                        ->itemLabel(fn (array $state): ?string => $state['inscricao_estadual'] . ' - ' . $state['estado']['sigla']  ?? null)
                                                                        ->collapsed()
                                                                        ->grid(2)
                                                                        ->addable(false)
                                                                        ->deletable(false)
                                                                        ->schema([
                                                                            Forms\Components\TextInput::make('inscricao_estadual')
                                                                                ->label('Inscrição Estadual'),
                                                                            Forms\Components\TextInput::make('atualizado_em')
                                                                                ->label('Data de Cadastramento'),
                                                                            Forms\Components\Toggle::make('ativo')
                                                                                ->label('Ativo?'),
                                                                            Forms\Components\TextInput::make('estado.sigla')
                                                                                ->label('Atividade primária'),
                                                                        ]),

                                                                ]),

                                                            Forms\Components\Fieldset::make('Sócios')
                                                                ->columns(1)
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('partners')
                                                                        ->label('')
                                                                        ->itemLabel(fn (array $state): ?string => $state['nome'] . ' - ' . $state['qualificacao_socio']['descricao']  ?? null)
                                                                        ->collapsed()
                                                                        ->grid(2)
                                                                        ->addable(false)
                                                                        ->deletable(false)
                                                                        ->schema([
                                                                            Forms\Components\TextInput::make('nome')
                                                                                ->label('Nome'),
                                                                            Forms\Components\TextInput::make('tipo')
                                                                                ->label('Tipo'),
                                                                            Forms\Components\TextInput::make('faixa_etaria')
                                                                                ->label('Faixa etária'),
                                                                            Forms\Components\TextInput::make('qualificacao_socio.descricao')
                                                                                ->label('Qualificação'),
                                                                        ]),
                                                                ]),
                                                        ]),
                                                ]),
                                            Forms\Components\Tabs\Tab::make('Dados adicionais')
                                                ->schema([
                                                    Forms\Components\Section::make()
                                                        ->relationship('company')
                                                        ->schema([


                                                            Forms\Components\Fieldset::make()
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('contacts')
                                                                        ->label('Contatos na empresa')
                                                                        ->columnSpanFull()
                                                                        ->columns(2)
                                                                        ->grid(2)
                                                                        ->collapsed()
                                                                        ->schema([
                                                                            Forms\Components\TextInput::make('sector')
                                                                                ->label('Setor')
                                                                                ->required(),

                                                                            Forms\Components\TextInput::make('name')
                                                                                ->label('Nome')
                                                                                ->required(),

                                                                            Forms\Components\TextInput::make('phone')
                                                                                ->label('Número')
                                                                                ->mask(
                                                                                    RawJs::make(
                                                                                        <<<'JS'
                                                                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                                                                    JS,
                                                                                    ),
                                                                                ),

                                                                            Forms\Components\TextInput::make('email')->label('E-mail'),
                                                                        ]),
                                                                ]),

                                                            Forms\Components\Fieldset::make()->schema([
                                                                Forms\Components\Repeater::make('phones')
                                                                    ->label('Telefones')
                                                                    ->columnSpanFull()
                                                                    ->columns(3)
                                                                    ->grid(2)
                                                                    ->collapsed()
                                                                    ->schema([
                                                                        Forms\Components\Select::make('sector')->label('Setor')->columnSpan(1)->required(),
                                                                        Forms\Components\TextInput::make('number')
                                                                            ->label('Número')
                                                                            ->mask(
                                                                                RawJs::make(
                                                                                    <<<'JS'
                                                            $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                        JS,
                                                                                ),
                                                                            )
                                                                            ->required()
                                                                            ->columnSpan(2),
                                                                    ]),
                                                            ]),

                                                            Forms\Components\Fieldset::make()->schema([
                                                                Forms\Components\Repeater::make('emails')
                                                                    ->label('E-mails adicionais')
                                                                    ->columnSpanFull()
                                                                    ->columns(3)
                                                                    ->grid(2)
                                                                    ->collapsed()
                                                                    ->schema([Forms\Components\TextInput::make('sector')->label('Setor')->columnSpan(1)->required(), Forms\Components\TextInput::make('email')->label('E-mail')->email()->required()->columnSpan(2)]),
                                                            ]),

                                                            Forms\Components\Fieldset::make()->schema([
                                                                Forms\Components\Repeater::make('websites')
                                                                    ->label('Websites adicionais')
                                                                    ->columnSpanFull()
                                                                    ->columns(1)
                                                                    ->grid(2)
                                                                    ->collapsed()
                                                                    ->schema([Forms\Components\TextInput::make('link')->label('Link')->helperText('Ex: https://google.com')->url()->required()]),
                                                            ]),

                                                            Forms\Components\Fieldset::make()
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('documents')
                                                                        ->label('Números de Documentos')
                                                                        ->columnSpanFull()
                                                                        ->columns(2)
                                                                        ->grid(2)
                                                                        ->collapsed()
                                                                        ->addActionLabel('Adicionar novo documento')
                                                                        ->schema([
                                                                            Forms\Components\Select::make('type')
                                                                                ->label('Tipo')
                                                                                ->options(DocumentTypeEnum::class)
                                                                                ->required(),
                                                                            Forms\Components\TextInput::make('number')->label('Número')->placeholder('1234567890')->hintIcon('heroicon-m-question-mark-circle', tooltip: 'No caso de OAB, informe o número e a UF. Ex: 12345-SP')->required(),
                                                                        ]),
                                                                ]),
                                                        ]),
                                                ]),
                                            Forms\Components\Tabs\Tab::make('Endereço')
                                                ->schema([
                                                    Forms\Components\Section::make()
                                                        ->relationship('company')
                                                        ->schema([


                                                            Forms\Components\Group::make()
                                                                ->columns(3)
                                                                ->schema([
                                                                    Forms\Components\TextInput::make('zipcode')->label('CEP')->mask('99999-999')->suffixAction(
                                                                        fn ($state, $set) => Forms\Components\Actions\Action::make('Buscar CEP')
                                                                            ->icon('heroicon-m-globe-alt')
                                                                            ->action(function () use ($state, $set) {
                                                                                $state = preg_replace('/[^0-9]/', '', $state);
                                                                                if (strlen($state) != 8) {
                                                                                    Notification::make()->danger()->title('Digite um cep valido')->send();
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
                                                                                    Notification::make()->danger()->title($e->getMessage())->send();
                                                                                }
                                                                            }),
                                                                    ),

                                                                    Forms\Components\Fieldset::make('Endereço')
                                                                        ->columnSpan(2)
                                                                        ->schema([Forms\Components\TextInput::make('street')->label('Rua, Via, Avenida...'), Forms\Components\TextInput::make('number')->label('Número'), Forms\Components\TextInput::make('complement')->label('Complemento'), Forms\Components\TextInput::make('neighborhood')->label('Bairro'), Forms\Components\TextInput::make('city')->label('Cidade')->disabled()->dehydrated(), Forms\Components\TextInput::make('state')->label('UF')->disabled()->dehydrated(), Forms\Components\TextInput::make('longitude')->label('Longitude'), Forms\Components\TextInput::make('latitude')->label('Latitude')]),

                                                                    Forms\Components\Repeater::make('addresses')
                                                                        ->label('Outros endereços')
                                                                        ->columnSpanFull()
                                                                        ->collapsed()
                                                                        ->addActionLabel('Registrar outros endereços relevantes')
                                                                        ->schema([
                                                                            Forms\Components\Group::make()
                                                                                ->columns(4)
                                                                                ->schema([
                                                                                    Forms\Components\TextInput::make('zipcode')->label('CEP')->columnSpan(1)->mask('99999-999')->suffixAction(
                                                                                        fn ($state, $set) => Forms\Components\Actions\Action::make('Buscar CEP')
                                                                                            ->icon('heroicon-m-globe-alt')
                                                                                            ->action(function () use ($state, $set) {
                                                                                                $state = preg_replace('/[^0-9]/', '', $state);
                                                                                                if (strlen($state) != 8) {
                                                                                                    Notification::make()->danger()->title('Digite um cep valido')->send();
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
                                                                                                    Notification::make()->danger()->title($e->getMessage())->send();
                                                                                                }
                                                                                            }),
                                                                                    ),
                                                                                    Forms\Components\TextInput::make('street')->label('Rua, Via, Avenida...')->columnSpan(2),
                                                                                    Forms\Components\TextInput::make('number')->label('Número')->columnSpan(1),
                                                                                    Forms\Components\TextInput::make('complement')->label('Complemento'),
                                                                                    Forms\Components\TextInput::make('neighborhood')->label('Bairro'),
                                                                                    Forms\Components\TextInput::make('city')->label('Cidade')->disabled()->dehydrated(),
                                                                                    Forms\Components\TextInput::make('state')->label('UF')->disabled()->dehydrated(),
                                                                                    Forms\Components\TextInput::make('longitude')->label('Longitude'),
                                                                                    Forms\Components\TextInput::make('latitude')->label('Latitude'),
                                                                                ]),
                                                                        ]),
                                                                ]),
                                                        ]),
                                                ]),

                                            Forms\Components\Tabs\Tab::make('Dados bancários')
                                                ->schema([
                                                    Forms\Components\Fieldset::make('Conta bancária')
                                                        ->relationship('company')
                                                        ->columns(4)
                                                        ->schema([
                                                            Forms\Components\Select::make('type_account_bank')
                                                                ->label('Tipo de conta')
                                                                ->columnSpan(1)
                                                                ->options(TypeOfBankAccountEnum::class),
                                                            Forms\Components\Select::make('bank_name')
                                                                ->label('Banco')
                                                                ->columnSpan(3)
                                                                ->options(
                                                                    Bank::all()->map(function ($bank) {
                                                                        return strtoupper($bank->compe . ' - ' . $bank->long_name);
                                                                    }),
                                                                )
                                                                ->searchable(),
                                                            Forms\Components\TextInput::make('bank_agency')->label('Agência')->columnSpan(2),
                                                            Forms\Components\TextInput::make('bank_account')->label('Conta')->columnSpan(2),
                                                            Forms\Components\TextInput::make('pix')->label('PIX')->columnSpanFull(),
                                                        ]),
                                                ]),

                                            Forms\Components\Tabs\Tab::make('Arquivos')
                                                ->schema([

                                                    Forms\Components\Repeater::make('attachments')
                                                        ->relationship()
                                                        ->label('Arquivos')
                                                        ->itemLabel(fn (array $state): ?string => $state['title']  ?? null)
                                                        ->collapsed()
                                                        ->grid(2)
                                                        ->addActionLabel('Anexar arquivo')
                                                        ->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('Título do arquivo')
                                                                ->placeholder('Descrição curta do documento')
                                                                ->maxLength(255),
                                                            Forms\Components\FileUpload::make('path')
                                                                ->label('Arquivo')
                                                                ->multiple()
                                                                ->openable()

                                                                ->maxSize('5120')
                                                                ->directory('clients/files')
                                                        ]),

                                                ]),

                                            Forms\Components\Tabs\Tab::make('Anotações')
                                                ->schema([
                                                    Forms\Components\Repeater::make('notes')
                                                        ->relationship()
                                                        ->label('Anotações')
                                                        // ->itemLabel(fn (array $state): ?string => $state['note']  ?? null)
                                                        ->collapsed()
                                                        ->deletable()
                                                        ->addActionLabel('Adicionar anotação')
                                                        ->schema([
                                                            Forms\Components\Hidden::make('user_id')
                                                                ->label('Autor')
                                                                ->default(auth()->user()->id),
                                                            Forms\Components\RichEditor::make('note')
                                                                ->label('Anotação')
                                                                ->required()
                                                                ->fileAttachmentsDirectory('clients/notes')
                                                                ->placeholder('Anotação...')
                                                                ->columnSpanFull(),
                                                        ]),
                                                ]),
                                        ]),
                                ]),
                            Forms\Components\Group::make()
                                ->relationship('company')
                                ->columns(1)
                                ->schema([
                                    Forms\Components\Section::make()->schema([
                                        Forms\Components\Fieldset::make('Imagem')->schema([
                                            Forms\Components\FileUpload::make('image')
                                                ->label('')
                                                ->columnSpanFull()
                                                ->image()
                                                ->imageEditor()
                                                ->imageEditorAspectRatios(['1:1'])
                                                ->directory('clients/images'),
                                        ]),

                                        Forms\Components\Fieldset::make('Status')->schema([Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true)]),
                                    ]),
                                ]),
                        ],
                        0 => []
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('12s')
            ->columns([

                // Tables\Columns\TextColumn::make('index')
                //     ->rowIndex()
                //     ->label('#')
                //     ->badge(),

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de cliente')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                // Tables\Columns\ViewColumn::make('cliente')
                //     ->label('Cliente')
                //     ->view('tables.columns.oie'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->placeholder(new HtmlString('
                        <div class="flex items-center  h-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 4">
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="0" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="1" begin="-.5"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="25" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="2" begin="-.4"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="50" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="3" begin="-.3"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="75" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="4" begin="-.2"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="100" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="5" begin="-.1"></animate>
                                </rect>
                                <rect fill="#7C3AED" stroke="#7C3AED" stroke-width="0" width="25" height="4" x="125" y="0">
                                    <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="6" begin="0"></animate>
                                </rect>
                            </svg>
                        </div>
                    '))
                    ->description(fn (Client $record): string => $record->document)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo'),

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
            ->defaultSort('id', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de cliente')
                    ->options(ClientTypeEnum::class),

            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            )
            ->actions([


                // Action::make('Editar')
                //     ->icon('heroicon-m-pencil-square')
                //     ->url(function (Client $record): string {
                //         if ($record->type == ClientTypeEnum::COMPANY) {
                //             return route('filament.admin.resources.client-companies.edit', $record->company->id);
                //         } else {
                //             return route('filament.admin.resources.client-individuals.edit', $record->individual->id);
                //         }
                //     }),

                Action::make('payment')
                    ->label('Pagamentos')
                    ->color('secondary')
                    ->icon('heroicon-o-currency-dollar')
                    ->model(FinancialPayment::class)
                    ->form([
                        Forms\Components\Fieldset::make()
                            ->schema([
                                Forms\Components\Hidden::make('user_id')
                                    ->label('Usuário')
                                    ->default(auth()->user()->id),

                                Forms\Components\Hidden::make('client_id')
                                    ->label('Usuário')
                                    ->default(
                                        fn (Client $record): int => $record->id
                                    ),

                                Forms\Components\Select::make('process_id')
                                    ->label('Processo')
                                    ->options(function (Client $record) {
                                        $processes = Process::where('client_id', $record->id)->get();
                                        return $processes->pluck('process', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Total')
                                    ->prefix('R$')
                                    ->numeric()
                                    ->required(),
                            ]),

                        Forms\Components\Fieldset::make('Entrada')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Select::make('entry_payment_method')
                                    ->label('Método de pagamento')
                                    ->options(PaymentMethodEnum::class)
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('entry_amount')
                                    ->label('Valor de entrada')
                                    ->prefix('R$')
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => $set('installment_amount', $get('amount') - $state))
                                    ->required(),

                                Forms\Components\DatePicker::make('entry_date')
                                    ->label('Data da entrada')
                                    ->default(Carbon::now())
                                    ->required(),
                            ]),

                        Forms\Components\Fieldset::make('Parcelamento')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('installments')
                                    ->label('Parcelas')
                                    ->suffixIcon('heroicon-o-x-mark')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->required()
                                    ->minValue(0)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?int $state, ?int $old): void {
                                        if (is_null($state)) {
                                            $set('payments', []);
                                            return;
                                        }

                                        $installmentAmount = $get('installment_amount');
                                        $payments = [];
                                        for ($i = 0; $i < $state; $i++) {
                                            $payments[] = [
                                                'amount_installment' => $installmentAmount / max($state, 1),
                                                'due_date_installment' => Carbon::parse($get('base_date_installment'))->addMonths($i + 1)->format('Y-m-d')
                                            ];
                                        }

                                        $set('payments', $payments);
                                    })
                                    ->afterStateHydrated(function (Set $set, Get $get) {
                                        $set('installments', 0);
                                    }),

                                Forms\Components\TextInput::make('installment_amount')
                                    ->label('Restante a parcelar')
                                    ->prefix('R$')
                                    ->numeric('0.00')
                                    ->required(),

                                Forms\Components\DatePicker::make('base_date_installment')
                                    ->label('Data base para parcelas')
                                    ->default(Carbon::now()->format('Y-m-d'))
                                    ->required(),
                            ]),

                        Forms\Components\Fieldset::make('Parcelas')

                            ->schema([
                                Forms\Components\Repeater::make('payments')
                                    // ->relationship('installments')
                                    ->hiddenLabel()
                                    ->deletable(false)
                                    ->addable(false)
                                    ->columnSpan(3)
                                    ->reorderable(false)
                                    ->columns(3)
                                    ->defaultItems(function (Get $get) {
                                        return $get('installments');
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('amount_installment')
                                            ->label('Valor da parcela')
                                            ->prefix('R$')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->required(),

                                        Forms\Components\DatePicker::make('due_date_installment')
                                            ->label('Vencimento da parcela')
                                            ->required(),
                                    ])
                            ]),
                    ])


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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        dd($this->getRecord());
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
