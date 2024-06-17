<?php

namespace App\Filament\Resources;

use Exception;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\ClientCompany;
use App\Enums\DocumentTypeEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Leandrocfe\FilamentPtbrFormFields\Money;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\ClientCompanyResource\Pages;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientCompanyResource\RelationManagers\ProcessesRelationManager;

class ClientCompanyResource extends Resource
{
    protected static ?string $model = ClientCompany::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static ?string $modelLabel = 'Pessoa Jurídica';

    protected static ?string $pluralModelLabel = 'Pessoa Jurídica';

    protected static ?string $navigationGroup = 'CLIENTES';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'company';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->columns(3)->schema([
            Forms\Components\Group::make()
                ->columnSpan(2)
                ->schema([
                    Forms\Components\Tabs::make('Tabs')
                        ->contained(true)
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('Empresa')->schema([
                                Forms\Components\Section::make()
                                    ->columns(6)
                                    ->schema([
                                        Forms\Components\TextInput::make('company')->label('Empresa')->columnSpan(3)->disabled(),

                                        Forms\Components\TextInput::make('fantasy_name')->label('Nome Fantasia')->columnSpan(3)->disabled(),

                                        Forms\Components\TextInput::make('email')->label('E-mail')->columnSpan(2)->email(),

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

                                        Forms\Components\TextInput::make('website')->label('Website/Rede Social')->columnSpan(2)->placeholder('https://www.site.com')->url(),
                                    ]),
                            ]),
                            Forms\Components\Tabs\Tab::make('CNPJ')->schema([
                                Forms\Components\Fieldset::make('Detalhes da empresa')
                                    ->columns(4)
                                    ->schema([Forms\Components\TextInput::make('company_size')->label('Porte da empresa')->columnSpan(2), Forms\Components\TextInput::make('legal_nature')->label('Natureza Jurídica')->columnSpan(2), Forms\Components\TextInput::make('type')->label('Tipo')->helperText('Matriz ou Filial')->columnSpan(2), Money::make('share_capital')->label('Capital social')->columnSpan(2)]),
                                Forms\Components\Fieldset::make('Estabelecimento')
                                    ->columns(2)
                                    ->schema([Forms\Components\TextInput::make('registration_status')->label('Situação Cadastral'), Forms\Components\DatePicker::make('registration_date')->label('Data de Cadastramento'), Forms\Components\DatePicker::make('activity_start_date')->label('Data de Início de Atividade'), Forms\Components\TextInput::make('main_activity')->label('Atividade primária')]),

                                Forms\Components\Fieldset::make('Inscricão Estadual')
                                    ->columns(2)
                                    ->schema([Forms\Components\TextInput::make('state_registration')->label('Inscrição Estadual'), Forms\Components\TextInput::make('state_registration_location')->label('Estado')]),

                                Forms\Components\Fieldset::make('Sócios')
                                    ->columns(2)
                                    ->schema([Forms\Components\TextInput::make('partner_name')->label('Sócio responsável'), Forms\Components\TextInput::make('partner_qualification')->label('Cargo'), Forms\Components\TextInput::make('partner_type')->label('Tipo de Sócio responsável')->helperText('Pessoa Física ou Juridica')]),
                            ]),
                            Forms\Components\Tabs\Tab::make('Dados adicionais')->schema([
                                Forms\Components\Fieldset::make()->schema([
                                    Forms\Components\Repeater::make('contacts')
                                        ->label('Contatos na empresa')
                                        ->columnSpanFull()
                                        ->columns(2)
                                        ->grid(2)
                                        ->collapsed()
                                        ->schema([
                                            Forms\Components\TextInput::make('sector')->label('Setor')->required(),

                                            Forms\Components\TextInput::make('name')->label('Nome')->required(),

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

                                Forms\Components\Fieldset::make()->schema([
                                    Forms\Components\Repeater::make('documents')
                                        ->label('Documentos')
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
                            Forms\Components\Tabs\Tab::make('Endereço')->schema([
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

                            Forms\Components\Tabs\Tab::make('Dados bancários')->schema([
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

                            Forms\Components\Tabs\Tab::make('Arquivos')->schema([
                                Forms\Components\Placeholder::make('attachments_placeholder')
                                    ->label('Anexos')
                                    ->content(function (ClientCompany $record): HtmlString {
                                        $files = $record->attachments;
                                        if ($files) {
                                            $filesList = '';
                                            foreach ($files as $note) {
                                                $filesList .= $note['title'] . ' - <a class="text-violet-500 hover:text-violet-600" href="' . Storage::url($note['file']) . '" target="_blank">' . $note['file'] . '</a></br>';

                                                return new HtmlString($filesList);
                                            }
                                        }

                                        return new HtmlString('Nenhum arquivo anexado');
                                    }),

                                Forms\Components\Repeater::make('attachments')
                                    ->label('Arquivos')
                                    ->collapsed()
                                    ->grid(2)
                                    ->addActionLabel('Anexar arquivo')
                                    ->schema([Forms\Components\TextInput::make('title')->label('Título do arquivo')->placeholder('Descrição curta do documento')->maxLength(255), Forms\Components\FileUpload::make('file')->label('Arquivo')->openable()->downloadable()->previewable()->maxSize('5120')->directory('clients/files')]),
                            ]),

                            Forms\Components\Tabs\Tab::make('Anotações')->schema([
                                Forms\Components\Placeholder::make('annotation_placeholder')
                                    ->label('Anotações')
                                    ->content(function (ClientCompany $record): HtmlString {
                                        $notes = $record->annotations;
                                        if ($notes) {
                                            $noteList = '';
                                            foreach ($notes as $note) {
                                                $noteList .= Carbon::parse($note['date']) . ' - ' . $note['author'] . ' - <span class="text-violet-500">' . $note['annotation'] . '</span></br>';

                                                return new HtmlString($noteList);
                                            }
                                        }
                                        return new HtmlString('Nenhuma anotação registrada');
                                    }),

                                Forms\Components\Repeater::make('annotations')
                                    ->label('Anotações')
                                    ->columns(2)
                                    ->collapsed()
                                    ->deletable()
                                    ->grid(2)
                                    ->addActionLabel('Adicionar anotação')
                                    ->schema([
                                        Forms\Components\Hidden::make('date')->label('Data')->default(Carbon::now()),
                                        Forms\Components\Hidden::make('author')
                                            ->label('Autor')
                                            ->default(auth()->user()->name),
                                        Forms\Components\Textarea::make('annotation')->label('Anotação')->required()->placeholder('Anotação...')->columnSpanFull(),
                                    ]),
                            ]),
                        ]),
                ]),
            Forms\Components\Group::make()
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Logo'),
                Tables\Columns\TextColumn::make('fantasy_name')->color('primary')->label('Empresa')->searchable()->sortable()->description(fn (ClientCompany $record): string => $record->client->document), Tables\Columns\TextColumn::make('company_size')->label('Natureza Jurídica')->searchable()->sortable()->description(fn (ClientCompany $record): string => $record->legal_nature),
                Tables\Columns\TextColumn::make('phone')->label('Telefone')->icon('heroicon-m-phone')->iconColor('primary')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->icon('heroicon-m-at-symbol')->iconColor('primary')->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Ativo')
            ])
            ->defaultSort('id', 'desc')
            ->filters([Tables\Filters\Filter::make('is_active')->label('Clientes ativos')->query(fn (Builder $query): Builder => $query->where('is_active', true)), Tables\Filters\Filter::make('company_size')->label('Tipo de empresa')->query(fn (Builder $query): Builder => $query->where('company_size', 'Matriz'))])
            ->actions([Tables\Actions\ActionGroup::make([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()->label('Contatos')])->button()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [ProcessesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientCompanies::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => Pages\EditClientCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_active', true);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                TextEntry::make('company')->label('Nome da empresa'),

                RepeatableEntry::make('contacts')
                    ->label('Empresa')
                    ->columns(2)
                    ->schema([TextEntry::make('sector')->label('Setor')->badge(), TextEntry::make('name')->label('Nome')->color('primary'), TextEntry::make('phone')->label('Telefone')->icon('heroicon-m-phone')->copyable()->copyMessage('Copiado')->copyMessageDuration(1500), TextEntry::make('email')->label('Email')->icon('heroicon-m-envelope')->copyable()->copyMessage('Copiado')->copyMessageDuration(1500)]),
            ]),
        ]);
    }
}
