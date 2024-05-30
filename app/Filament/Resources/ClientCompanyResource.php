<?php

namespace App\Filament\Resources;

use App\Enums\DocumentTypeEnum;
use App\Enums\TypeOfBankAccountEnum;
use App\Filament\Resources\ClientCompanyResource\Pages;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Models\Bank;
use App\Models\ClientCompany;
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
use Leandrocfe\FilamentPtbrFormFields\Money;

class ClientCompanyResource extends Resource
{
    protected static ?string $model = ClientCompany::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static ?string $modelLabel = 'Pessoa Jurídica';

    protected static ?string $pluralModelLabel = 'Pessoa Jurídica';

    protected static ?string $navigationGroup = 'CLIENTES';

    protected static ?int $navigationSort = 1;

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
                                Forms\Components\Tabs\Tab::make('Dados da empresa')
                                    ->schema([
                                        Forms\Components\Section::make()
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
                                                    ->mask(RawJs::make(<<<'JS'
                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                    JS)),

                                                Forms\Components\TextInput::make('website')
                                                    ->label('Website/Rede Social')
                                                    ->columnSpan(2)
                                                    ->placeholder('https://www.site.com')
                                                    ->url(),
                                            ]),
                                        Forms\Components\Section::make()
                                            ->columns(4)
                                            ->schema([
                                                Forms\Components\TextInput::make('company_size')
                                                    ->label('Porte da empresa')
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('legal_nature')
                                                    ->label('Natureza Jurídica')
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('type')
                                                    ->label('Tipo')
                                                    ->helperText('Matriz ou Filial')
                                                    ->columnSpan(2),

                                                Money::make('share_capital')
                                                    ->label('Capital social')
                                                    ->columnSpan(2),
                                            ]),
                                        Forms\Components\Fieldset::make('Estabelecimento')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('registration_status')
                                                    ->label('Situação Cadastral'),

                                                Forms\Components\DatePicker::make('registration_date')
                                                    ->label('Data de Cadastramento'),

                                                Forms\Components\DatePicker::make('activity_start_date')
                                                    ->label('Data de Início de Atividade'),

                                                Forms\Components\TextInput::make('main_activity')
                                                    ->label('Atividade primária'),
                                            ]),

                                        Forms\Components\Fieldset::make('Inscricão Estadual')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('state_registration')
                                                    ->label('Inscrição Estadual'),

                                                Forms\Components\TextInput::make('state_registration_location')
                                                    ->label('Estado'),

                                                Forms\Components\TextInput::make('partner_name')
                                                    ->label('Sócio responsável'),

                                                Forms\Components\TextInput::make('partner_qualification')
                                                    ->label('Cargo'),

                                                Forms\Components\TextInput::make('partner_type')
                                                    ->label('Tipo de Sócio responsável')
                                                    ->helperText('Pessoa Física ou Juridica'),

                                            ]),

                                    ]),
                                Forms\Components\Tabs\Tab::make('Dados adicionais')
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
                                                            ->mask(RawJs::make(<<<'JS'
                                                                    $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                                JS)),

                                                        Forms\Components\TextInput::make('email')
                                                            ->label('E-mail'),

                                                    ]),
                                            ]),

                                        Forms\Components\Fieldset::make()
                                            ->schema([

                                                Forms\Components\Repeater::make('phones')
                                                    ->label('Telefones')
                                                    ->columnSpanFull()
                                                    ->columns(3)
                                                    ->grid(2)
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\Select::make('sector')
                                                            ->label('Setor')
                                                            ->columnSpan(1)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('number')
                                                            ->label('Número')
                                                            ->mask(RawJs::make(<<<'JS'
                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                    JS))
                                                            ->required()
                                                            ->columnSpan(2),
                                                    ]),
                                            ]),

                                        Forms\Components\Fieldset::make()
                                            ->schema([

                                                Forms\Components\Repeater::make('emails')
                                                    ->label('E-mails adicionais')
                                                    ->columnSpanFull()
                                                    ->columns(3)
                                                    ->grid(2)
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('sector')
                                                            ->label('Setor')
                                                            ->columnSpan(1)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('email')
                                                            ->label('E-mail')
                                                            ->email()
                                                            ->required()
                                                            ->columnSpan(2),
                                                    ]),

                                            ]),

                                        Forms\Components\Fieldset::make()
                                            ->schema([
                                                Forms\Components\Repeater::make('websites')
                                                    ->label('Websites adicionais')
                                                    ->columnSpanFull()
                                                    ->columns(1)
                                                    ->grid(2)
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('link')
                                                            ->label('Link')
                                                            ->helperText('Ex: https://google.com')
                                                            ->url()
                                                            ->required(),
                                                    ]),

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
                                                        return strtoupper($bank->compe.' - '.$bank->long_name);
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
                                                    ->disabled(! auth()->user()->hasRole('Root'))
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
                Tables\Columns\TextColumn::make('company')
                    ->color('primary')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientCompany $record): string => $record->client->document),

                Tables\Columns\TextColumn::make('company_size')
                    ->label('Natureza Jurídica')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientCompany $record): string => $record->legal_nature),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->icon('heroicon-m-phone')
                    ->iconColor('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-at-symbol')
                    ->iconColor('primary')
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Clientes ativos')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),

                Tables\Filters\Filter::make('company_size')
                    ->label('Tipo de empresa')
                    ->query(fn (Builder $query): Builder => $query->where('company_size', 'Matriz')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClientCompanies::route('/'),
            // 'create' => Pages\CreateClientCompany::route('/create'),
            'create' => CreateClient::route('/create'),
            'edit' => Pages\EditClientCompany::route('/{record}/edit'),
        ];
    }
}
