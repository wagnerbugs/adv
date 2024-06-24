<?php

namespace App\Filament\Resources;

use Exception;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ClientTypeEnum;
use Filament\Resources\Resource;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Leandrocfe\FilamentPtbrFormFields\Document;
use App\Filament\Resources\SupplierResource\Pages;
use Leandrocfe\FilamentPtbrFormFields\PhoneNumber;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Traits\CapitalizeTrait;

class SupplierResource extends Resource
{

    use CapitalizeTrait;

    protected static ?string $model = Supplier::class;

    protected static ?string $modelLabel = 'Fornecedor';

    protected static ?string $pluralModelLabel = 'Fornecedores';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'CADASTROS';

    protected static ?string $navigationLabel = 'Fornecedores';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static bool $isGloballySearchable = true;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'document'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Phone' => $record->phone,
            'E-mail' => $record->email,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpan(4)
                    ->schema([

                        Forms\Components\Tabs::make()
                            ->tabs([

                                Forms\Components\Tabs\Tab::make('Fornecedor')
                                    ->columns(3)
                                    ->schema([

                                        Forms\Components\Fieldset::make()
                                            ->columns(3)
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->label('Tipo de fornecedor')
                                                    ->visibleOn('create')
                                                    ->options(ClientTypeEnum::class)
                                                    ->default(ClientTypeEnum::COMPANY)
                                                    ->required(),

                                                Document::make('document')
                                                    ->label('CPF/CNPJ')
                                                    ->required()
                                                    ->dynamic()
                                                    ->suffixAction(
                                                        fn ($state, $set) => Forms\Components\Actions\Action::make('Buscar CNPJ')
                                                            ->icon('heroicon-m-globe-alt')
                                                            ->action(
                                                                function () use ($state, $set) {
                                                                    $state = preg_replace('/[^0-9]/', '', $state);
                                                                    if (strlen($state) != 14) {
                                                                        Notification::make()
                                                                            ->danger()
                                                                            ->title('Digite um CNPJ válido')
                                                                            ->send();
                                                                    }

                                                                    try {
                                                                        $response = Http::get('https://publica.cnpj.ws/cnpj/' . $state);
                                                                        $data = $response->json();

                                                                        $phone = $data['estabelecimento']['ddd1'] . $data['estabelecimento']['telefone1'];
                                                                        $street = $data['estabelecimento']['tipo_logradouro'] . ' ' . $data['estabelecimento']['logradouro'];

                                                                        $set('name', $data['razao_social']);
                                                                        $set('phone', $phone);
                                                                        $set('email', $data['estabelecimento']['email']);
                                                                        $set('zipcode', $data['estabelecimento']['cep']);
                                                                        $set('street', $street);
                                                                        $set('number', $data['estabelecimento']['numero']);
                                                                        $set('complement', $data['estabelecimento']['complemento']);
                                                                        $set('neighborhood', $data['estabelecimento']['bairro']);
                                                                        $set('state', $data['estabelecimento']['estado']['sigla']);
                                                                        $set('city', $data['estabelecimento']['cidade']['nome']);
                                                                    } catch (Exception $e) {
                                                                        Notification::make()
                                                                            ->danger()
                                                                            ->title($e->getMessage())
                                                                            ->send();
                                                                    }
                                                                }
                                                            )
                                                    ),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome do fornecedor')
                                                    ->required()
                                                    ->maxLength(255),

                                            ]),

                                        Forms\Components\Fieldset::make('Contato')
                                            ->columns(3)
                                            ->schema([

                                                PhoneNumber::make('phone')
                                                    ->label('Telefone'),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('website')
                                                    ->maxLength(255),
                                            ])
                                    ]),

                                Forms\Components\Tabs\Tab::make('Outros dados')
                                    ->schema([
                                        Forms\Components\Repeater::make('phones')
                                            ->grid(3)
                                            ->label('Telefones')
                                            ->itemLabel(fn (array $state): ?string => $state['phone'] ?? null)
                                            ->collapsed()
                                            ->schema([
                                                PhoneNumber::make('phone')
                                                    ->label('Telefone'),
                                            ]),

                                        Forms\Components\Repeater::make('emails')
                                            ->grid(3)
                                            ->label('E-mails')
                                            ->itemLabel(fn (array $state): ?string => $state['email'] ?? null)
                                            ->collapsed()
                                            ->schema([
                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->email(),
                                            ]),

                                        Forms\Components\Repeater::make('websites')
                                            ->grid(3)
                                            ->label('Websites')
                                            ->itemLabel(fn (array $state): ?string => $state['website'] ?? null)
                                            ->collapsed()
                                            ->schema([
                                                Forms\Components\TextInput::make('website')
                                                    ->label('Website')
                                                    ->url(),
                                            ]),

                                        Forms\Components\Repeater::make('contacts')
                                            ->grid(3)
                                            ->label('Contatos')
                                            ->itemLabel(fn (array $state): ?string => $state['sector'] ?? null)
                                            ->collapsed()
                                            ->schema([
                                                Forms\Components\TextInput::make('sector')
                                                    ->label('Setor'),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome'),

                                                PhoneNumber::make('phone')
                                                    ->label('Telefone'),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('E-mail')
                                                    ->email(),
                                            ]),

                                        Forms\Components\Repeater::make('attachments')
                                            ->grid(3)
                                            ->label('Arquivos')
                                            ->schema([
                                                Forms\Components\FileUpload::make('attachment')
                                                    ->label('Arquivo')
                                                    ->downloadable()
                                                    ->previewable()
                                                    ->directory('suppliers/attachments'),
                                            ]),

                                        Forms\Components\Repeater::make('annotations')
                                            ->grid(3)
                                            ->label('Anotações')
                                            ->schema([
                                                Forms\Components\Hidden::make('author')
                                                    ->default(auth()->user()->name),
                                                Forms\Components\Hidden::make('date')
                                                    ->default(Carbon::now()->toDateTimeString()),
                                                Forms\Components\Textarea::make('annotation')
                                                    ->label('Anotação'),
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

                                    ]),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Logo')
                                    ->avatar()
                                    ->directory('suppliers'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Ativo?')
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Logo'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de fornecedor')
                    ->badge(),

                Tables\Columns\TextColumn::make('document')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Fornecedor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo?'),

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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
