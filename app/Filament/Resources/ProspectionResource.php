<?php

namespace App\Filament\Resources;

use Exception;
use Filament\Forms;
use App\Models\Bank;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\GenderEnum;
use Filament\Tables\Table;
use App\Models\Prospection;
use Filament\Support\RawJs;
use App\Enums\MaritalStatusEnum;
use Filament\Resources\Resource;
use App\Enums\EducationLevelEnum;
use Illuminate\Support\HtmlString;
use App\Enums\TreatmentPronounEnum;
use App\Enums\ProspectionStatusEnum;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Support\Facades\Http;
use App\Enums\ProspectionReactionEnum;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Leandrocfe\FilamentPtbrFormFields\Document;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProspectionResource\Pages;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;
use App\Filament\Resources\ProspectionResource\RelationManagers;
use App\Models\ProspectionCompany;
use App\Models\ProspectionProcess;
use Carbon\Carbon;
use Filament\Forms\Components\Repeater;

class ProspectionResource extends Resource
{
    protected static ?string $model = Prospection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Prospecção';

    protected static ?string $pluralModelLabel = 'Prospecções';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([

                Forms\Components\Group::make()
                    ->columnSpan(4)
                    ->schema([

                        Forms\Components\Tabs::make('Tabs')
                            ->columnSpan(3)
                            ->tabs([

                                Forms\Components\Tabs\Tab::make('Dados da Prospeção')
                                    ->schema([

                                        Forms\Components\Fieldset::make()
                                            ->schema([
                                                Forms\Components\Select::make('user_id')
                                                    ->label('Usuário')
                                                    ->relationship('user', 'name')
                                                    ->default(auth()->user()->id)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required(),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Descritivo')
                                                    ->columnSpanFull()
                                                    ->maxLength(255)
                                                    ->required(),
                                            ]),
                                        Forms\Components\Fieldset::make('PESQUISA')
                                            ->columns(3)
                                            ->schema([
                                                Document::make('cnpj')
                                                    ->label('CNPJ')
                                                    ->cnpj()
                                                    ->prefixIcon('heroicon-m-building-office')
                                                    ->prefixIconColor('primary')
                                                    ->mask('99.999.999/9999-99')
                                                    ->unique(table: 'prospections', column: 'cnpj', ignoreRecord: true)
                                                    ->disabledOn('edit')
                                                    ->dehydrated(),
                                                Document::make('cpf')
                                                    ->label('CPF')
                                                    ->cpf()
                                                    ->prefixIcon('heroicon-m-user')
                                                    ->prefixIconColor('primary')
                                                    ->mask('999.999.999-99')
                                                    ->unique(table: 'prospections', column: 'cpf', ignoreRecord: true)
                                                    ->disabledOn('edit')
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make('process')
                                                    ->label('Processo')
                                                    ->prefixIcon('heroicon-m-academic-cap')
                                                    ->prefixIconColor('primary')
                                                    ->mask('9999999-99.9999.9.99.9999')
                                                    ->unique(table: 'prospections', column: 'process', ignoreRecord: true)
                                                    ->disabledOn('edit')
                                                    ->dehydrated(),
                                            ]),


                                    ]),

                                Forms\Components\Tabs\Tab::make('Processo')
                                    ->visibleOn('edit')
                                    ->icon('heroicon-m-building-library')
                                    ->schema([

                                        Forms\Components\Repeater::make('processes')
                                            ->label('')
                                            ->relationship()
                                            // ->collapsed()
                                            ->schema([

                                                Forms\Components\Tabs::make('Tabs')
                                                    ->contained(false)
                                                    ->columnSpanFull()
                                                    ->tabs([

                                                        Forms\Components\Tabs\Tab::make('Dados do processo')
                                                            ->columns(3)
                                                            ->schema([

                                                                Forms\Components\Fieldset::make('CAPA DO PROCESSO')
                                                                    ->schema([

                                                                        Forms\Components\Placeholder::make('process')
                                                                            ->label('')
                                                                            ->content(
                                                                                fn (ProspectionProcess $record): HtmlString => new HtmlString(
                                                                                    'Processo: <strong class="text-violet-500">' .  $record->process
                                                                                        . '</strong>'
                                                                                )
                                                                            ),


                                                                        Forms\Components\Placeholder::make('prospect_process_number_date_autation')
                                                                            ->label('')
                                                                            ->content(fn (ProspectionProcess $record): HtmlString => new HtmlString(' Data de autuação: <strong class="text-violet-500">' .  Carbon::parse($record->dataAjuizamento)->format('d/m/Y H:i:s') . '</strong>')),

                                                                        Forms\Components\Placeholder::make('judging_organ')
                                                                            ->label('')
                                                                            ->content(
                                                                                fn (ProspectionProcess $record): HtmlString => new HtmlString('Órgão Julgador: <strong class="text-violet-500">' . $record->orgaoJulgador['nome'] . '</strong>')
                                                                            ),

                                                                        Forms\Components\Placeholder::make('class_name')
                                                                            ->label('')
                                                                            ->content(
                                                                                fn (ProspectionProcess $record): HtmlString => new HtmlString('Classe da ação: <strong class="text-violet-500">' . $record->classe['nome'] . '</strong>')
                                                                            ),

                                                                        Forms\Components\Placeholder::make('process_api_id')
                                                                            ->label('')
                                                                            ->content(
                                                                                fn (ProspectionProcess $record): HtmlString => new HtmlString('ID: <strong class="text-violet-500">' . $record->process_api_id . '</strong>')
                                                                            ),

                                                                        Forms\Components\Placeholder::make('tribunal')
                                                                            ->label('')
                                                                            ->content(
                                                                                fn (ProspectionProcess $record): HtmlString => new HtmlString('Tribunal: <strong class="text-violet-500">' . $record->tribunal . ' - ' . $record->grau . '</strong>')
                                                                            ),

                                                                    ]),
                                                                Forms\Components\Fieldset::make('Assuntos')
                                                                    ->schema([
                                                                        Forms\Components\Placeholder::make('subjects')
                                                                            ->label('')
                                                                            ->columnSpanFull()
                                                                            ->content(
                                                                                function (ProspectionProcess $record): HtmlString {
                                                                                    $subjects = $record->assuntos;
                                                                                    $subjectList = '<ul>';
                                                                                    foreach ($subjects as $subject) {
                                                                                        $subjectList .=  '<li>' . $subject['codigo'] . ' - <span class="text-violet-500 font-bold">' . $subject['nome'] . '</span></li>';
                                                                                    }
                                                                                    $subjectList .= '</ul>';

                                                                                    return new HtmlString($subjectList);
                                                                                }
                                                                            ),
                                                                    ]),
                                                            ]),
                                                        Forms\Components\Tabs\Tab::make('Movimentos')
                                                            ->schema([

                                                                Forms\Components\Fieldset::make('Movimentos')
                                                                    ->schema([
                                                                        Forms\Components\Placeholder::make('moviments_list')
                                                                            ->label('')
                                                                            ->columnSpanFull()
                                                                            ->content(
                                                                                function (ProspectionProcess $record): HtmlString {
                                                                                    $moviments = $record->movimentos;
                                                                                    $movimentsList = '<div class="relative overflow-x-auto shadow-md sm:rounded-lg"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Data</th><th scope="col" class="px-6 py-3">Código</th><th scope="col" class="px-6 py-3">Movimento</th></tr></thead><tbody>';
                                                                                    foreach ($moviments as $moviment) {
                                                                                        $movimentsList .=  '
                                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . Carbon::parse($moviment['dataHora'])->format('d/m/Y H:i:s') . '</th><td class="px-6 py-4">' . $moviment['codigo'] . '</td><td class="px-6 py-4 text-gray-900 dark:text-white font-bold">' . $moviment['nome'] . '</td></tr>';
                                                                                    }
                                                                                    $movimentsList .= '</tbody></table></div>';

                                                                                    return new HtmlString($movimentsList);
                                                                                }
                                                                            ),
                                                                    ]),
                                                            ]),

                                                    ]),




                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('CPF')
                                    ->visibleOn('edit')
                                    ->icon('heroicon-m-user')
                                    ->schema([

                                        Forms\Components\Tabs::make('Tabs')
                                            ->contained(false)
                                            ->columnSpanFull()
                                            ->tabs([

                                                Forms\Components\Tabs\Tab::make('Dados pessoais')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('individual')
                                                            ->schema([

                                                                Forms\Components\Section::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        Forms\Components\Select::make('title')
                                                                            ->label('Título')
                                                                            ->searchable()
                                                                            ->options(TreatmentPronounEnum::class),

                                                                        Forms\Components\TextInput::make('nome')
                                                                            ->label('Nome')
                                                                            ->disabled(),

                                                                        Document::make('cpf')
                                                                            ->label('CPF')
                                                                            ->cpf()
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                    ]),

                                                                Forms\Components\Section::make()
                                                                    ->columns(4)
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('sexo')
                                                                            ->label('Gênero'),

                                                                        Forms\Components\DatePicker::make('data_nascimento')
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
                                                                        Forms\Components\TextInput::make('mae')
                                                                            ->label('Nome da mãe'),
                                                                        Forms\Components\TextInput::make('father_name')
                                                                            ->label('Nome do pai'),
                                                                    ]),

                                                                Forms\Components\Fieldset::make('Ocupação')
                                                                    ->columns(2)
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('workplace')
                                                                            ->label('Empresa'),
                                                                        Forms\Components\TextInput::make('ocupation')
                                                                            ->label('Profissão'),
                                                                    ]),

                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Dados adicionais')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('individual')
                                                            ->schema([

                                                                Forms\Components\Fieldset::make('Telefones Fixo')
                                                                    ->columns(1)
                                                                    ->schema([

                                                                        Forms\Components\Repeater::make('pesquisa_telefones_fixo.telefone')
                                                                            ->label('')
                                                                            ->addActionLabel('Adicionar')
                                                                            ->columns(3)
                                                                            ->collapsed()
                                                                            ->grid(2)
                                                                            ->default(function ($get) {
                                                                                $telefones = $get('pesquisa_telefones_fixo.telefone');
                                                                                if (isset($telefones['numero'])) {
                                                                                    // Se houver apenas um telefone, transforma em array
                                                                                    return [$telefones];
                                                                                }
                                                                                return $telefones;
                                                                            })
                                                                            ->afterStateHydrated(function ($state, callable $set) {
                                                                                if (isset($state['numero'])) {
                                                                                    // Se houver apenas um telefone, transforma em array
                                                                                    $set('pesquisa_telefones_fixo.telefone', [$state]);
                                                                                }
                                                                            })
                                                                            ->schema([
                                                                                Forms\Components\Hidden::make('img')
                                                                                    ->label('Imagem'),
                                                                                Forms\Components\TextInput::make('numero')
                                                                                    ->label('Número')
                                                                                    ->mask(RawJs::make(<<<'JS'
                                                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                                                    JS))
                                                                                    ->required(),
                                                                                Forms\Components\Hidden::make('tem_zap')
                                                                                    ->label('Whatsapp?'),
                                                                                Forms\Components\Hidden::make('tipo_tel')
                                                                                    ->label('Tipo de Telefone'),
                                                                                Forms\Components\TextInput::make('operadora')
                                                                                    ->label('Operadora'),
                                                                                Forms\Components\TextInput::make('prioridade')
                                                                                    ->label('Prioridade'),
                                                                                Forms\Components\Hidden::make('nao_pertube')
                                                                                    ->label('Não Perturbe'),
                                                                                Forms\Components\Hidden::make('data_referencia')
                                                                                    ->label('Data de Referência'),
                                                                            ]),
                                                                    ]),

                                                                Forms\Components\Fieldset::make('Telefones Celular')
                                                                    ->columns(1)
                                                                    ->schema([
                                                                        Forms\Components\Repeater::make('pesquisa_telefones_celular.telefone')
                                                                            ->label('')
                                                                            ->addActionLabel('Adicionar')
                                                                            ->columns(3)
                                                                            ->collapsed()
                                                                            ->grid(2)
                                                                            ->default(function ($get) {
                                                                                $telefones = $get('pesquisa_telefones_fixo.telefone');
                                                                                if (isset($telefones['numero'])) {
                                                                                    // Se houver apenas um telefone, transforma em array
                                                                                    return [$telefones];
                                                                                }
                                                                                return $telefones;
                                                                            })
                                                                            ->afterStateHydrated(function ($state, callable $set) {
                                                                                if (isset($state['numero'])) {
                                                                                    // Se houver apenas um telefone, transforma em array
                                                                                    $set('pesquisa_telefones_fixo.telefone', [$state]);
                                                                                }
                                                                            })
                                                                            ->schema([
                                                                                Forms\Components\Hidden::make('img')
                                                                                    ->label('Imagem'),
                                                                                Forms\Components\TextInput::make('numero')
                                                                                    ->label('Número')
                                                                                    ->mask(RawJs::make(<<<'JS'
                                                                                        $input.length >= 15 ? '(99) 99999-9999' : '(99) 9999-9999'
                                                                                    JS))
                                                                                    ->required(),
                                                                                Forms\Components\Hidden::make('tem_zap')
                                                                                    ->label('Whatsapp?'),
                                                                                Forms\Components\Hidden::make('tipo_tel')
                                                                                    ->label('Tipo de Telefone'),
                                                                                Forms\Components\TextInput::make('operadora')
                                                                                    ->label('Operadora'),
                                                                                Forms\Components\TextInput::make('prioridade')
                                                                                    ->label('Prioridade'),
                                                                                Forms\Components\Hidden::make('nao_pertube')
                                                                                    ->label('Não Perturbe'),
                                                                                Forms\Components\Hidden::make('data_referencia')
                                                                                    ->label('Data de Referência'),
                                                                            ]),
                                                                    ]),

                                                                Forms\Components\Fieldset::make('E-mails')
                                                                    ->columns(1)
                                                                    ->schema([
                                                                        Forms\Components\Repeater::make('emails')
                                                                            ->label('')
                                                                            ->schema([
                                                                                Forms\Components\TextInput::make('email')
                                                                            ]),
                                                                    ]),
                                                            ]),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('Endereço')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->schema([

                                                                Forms\Components\Fieldset::make('Endereços')
                                                                    ->columns(1)
                                                                    ->schema([

                                                                        Forms\Components\Group::make()
                                                                            ->columns(3)
                                                                            ->relationship('individual')
                                                                            ->schema([

                                                                                Forms\Components\Repeater::make('pesquisa_enderecos.endereco')
                                                                                    ->label('')
                                                                                    ->columnSpanFull()
                                                                                    ->collapsed()
                                                                                    ->addActionLabel('Adicionar')
                                                                                    ->schema([
                                                                                        Forms\Components\Group::make()
                                                                                            ->columns(4)
                                                                                            ->schema([
                                                                                                Forms\Components\TextInput::make('cep')
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

                                                                                                                        $set('logradouro', $data['street']);
                                                                                                                        $set('bairro', $data['neighborhood']);
                                                                                                                        $set('cidade', $data['city']);
                                                                                                                        $set('estado', $data['state']);
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
                                                                                                Forms\Components\TextInput::make('logradouro')
                                                                                                    ->label('Rua, Via, Avenida...')
                                                                                                    ->columnSpan(2),
                                                                                                Forms\Components\TextInput::make('numero')
                                                                                                    ->label('Número')
                                                                                                    ->columnSpan(1),
                                                                                                Forms\Components\TextInput::make('complemento')
                                                                                                    ->label('Complemento'),
                                                                                                Forms\Components\TextInput::make('bairro')
                                                                                                    ->label('Bairro'),
                                                                                                Forms\Components\TextInput::make('cidade')
                                                                                                    ->label('Cidade')
                                                                                                    ->disabled()
                                                                                                    ->dehydrated(),
                                                                                                Forms\Components\TextInput::make('estado')
                                                                                                    ->label('UF')
                                                                                                    ->disabled()
                                                                                                    ->dehydrated(),
                                                                                                Forms\Components\TextInput::make('longitude')
                                                                                                    ->label('Longitude'),
                                                                                                Forms\Components\TextInput::make('latitude')
                                                                                                    ->label('Latitude'),
                                                                                            ]),
                                                                                    ]),

                                                                                FilamentJsonColumn::make('pesquisa_enderecos.endereco')
                                                                                    ->label('Endereços')
                                                                                    ->columnSpanFull(),
                                                                            ]),
                                                                    ]),
                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Participações')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('individual')
                                                            ->schema([

                                                                Forms\Components\Repeater::make('alerta_participacoes.empresa_socio')
                                                                    ->label('')
                                                                    ->collapsed()
                                                                    ->addActionLabel('Adicionar')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('cnpj')
                                                                            ->label('CNPJ'),
                                                                        Forms\Components\TextInput::make('cidade')
                                                                            ->label('Cidade'),
                                                                        Forms\Components\TextInput::make('data_entrada')
                                                                            ->label('Data de entrada'),
                                                                        Forms\Components\TextInput::make('qualificacao')
                                                                            ->label('Qualificação'),
                                                                        Forms\Components\TextInput::make('razao_social')
                                                                            ->label('Razão Social'),
                                                                        Forms\Components\TextInput::make('valor_participacao')
                                                                            ->label('Valor da participação'),
                                                                    ]),

                                                                FilamentJsonColumn::make('alerta_participacoes')
                                                                    ->label('Alerta de Participação'),

                                                            ]),
                                                    ]),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make('CNPJ')
                                    ->visibleOn('edit')
                                    ->icon('heroicon-m-building-storefront')
                                    ->schema([

                                        Forms\Components\Tabs::make('Tabs')
                                            ->contained(false)
                                            ->columnSpanFull()
                                            ->tabs([

                                                Forms\Components\Tabs\Tab::make('Dados da empresa')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('company')
                                                            ->schema([

                                                                Forms\Components\Section::make()
                                                                    ->columns(3)
                                                                    ->schema([


                                                                        Document::make('cnpj')
                                                                            ->label('Nome')
                                                                            ->cnpj()
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                        Forms\Components\TextInput::make('tipo')
                                                                            ->label('Tipo')
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                        Forms\Components\TextInput::make('nome_fantasia')
                                                                            ->label('Nome Fantasia')
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                        Forms\Components\TextInput::make('razao_social')
                                                                            ->label('Razão Social')
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                        Forms\Components\TextInput::make('capital_social')
                                                                            ->label('Capital Social')
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                        Forms\Components\TextInput::make('atualizado_em')
                                                                            ->label('Atualizado em')
                                                                            ->disabled()
                                                                            ->dehydrated(),

                                                                    ]),

                                                                Forms\Components\Section::make()
                                                                    ->columns(4)
                                                                    ->schema([

                                                                        Forms\Components\Placeholder::make('porte')
                                                                            ->label('Porte')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                $porte = $record->porte;
                                                                                $porteDescricao = isset($porte['descricao']) ? $porte['descricao'] : 'Descrição não disponível';

                                                                                return new HtmlString($porteDescricao);
                                                                            }),

                                                                        Forms\Components\Placeholder::make('natureza_juridica')
                                                                            ->label('Natureza Jurídica')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                $natureza = $record->natureza_juridica;
                                                                                $naturezaJuridica = isset($natureza['descricao']) ? $natureza['descricao'] : 'Descrição não disponível';

                                                                                return new HtmlString($naturezaJuridica);
                                                                            }),

                                                                        Forms\Components\Placeholder::make('qualificacao_do_responsavel')
                                                                            ->label('Qualificação do Responsável')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                $qualificacao = $record->qualificacao_do_responsavel;
                                                                                $qualificacaoResponsavel = isset($qualificacao['descricao']) ? $qualificacao['descricao'] : 'Descrição não disponível';

                                                                                return new HtmlString($qualificacaoResponsavel);
                                                                            }),

                                                                        Forms\Components\Placeholder::make('situacao_cadastral')
                                                                            ->label('Situacao Cadastral')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                $situacao = $record->situacao_cadastral;
                                                                                return new HtmlString($situacao);
                                                                            }),

                                                                    ]),

                                                                Forms\Components\Section::make()
                                                                    ->columns(1)
                                                                    ->schema([
                                                                        Forms\Components\Placeholder::make('atividade_principal')
                                                                            ->label('Atividade Primária')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                if ($record->atividade_principal) {
                                                                                    $atividadePrimaria = $record->atividade_principal;
                                                                                    $descricao = $atividadePrimaria['descricao'];
                                                                                    $subclasse = $atividadePrimaria['subclasse'];
                                                                                    return new HtmlString($descricao . ' - ' . $subclasse);
                                                                                }
                                                                                return new HtmlString('Não definido');
                                                                            }),
                                                                    ])


                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Sócios')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('company')
                                                            ->schema([

                                                                Forms\Components\Section::make()
                                                                    ->columns(1)
                                                                    ->schema([

                                                                        Forms\Components\Placeholder::make('socios')
                                                                            ->label('Informações do Sócio')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {

                                                                                if ($record->socios) {
                                                                                    $socios = json_decode($record->socios, true);



                                                                                    $content = '<div class="relative overflow-x-auto"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Sócio</th><th scope="col" class="px-6 py-3">Data de entrada</th><th scope="col" class="px-6 py-3">Idade</th><th scope="col" class="px-6 py-3">Cargo</th></tr></thead><tbody>';
                                                                                    foreach ($socios as $socio) {
                                                                                        $content .= '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . $socio['nome'] . ' - <small>' . $socio['cpf_cnpj_socio'] . '</small></th><td class="px-6 py-4">' . Carbon::parse($socio['data_entrada'])->format('d/m/Y') . '</td><td class="px-6 py-4">' . $socio['faixa_etaria'] . '</td><td class="px-6 py-4">' . $socio['qualificacao_socio']['descricao'] . '</td></tr>';
                                                                                    }

                                                                                    $content .= '</tbody></table></div>';


                                                                                    return new HtmlString($content);
                                                                                }
                                                                                return new HtmlString('Não definido');
                                                                            }),

                                                                    ]),


                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Atividades')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('company')
                                                            ->schema([

                                                                Forms\Components\Section::make()
                                                                    ->columns(1)
                                                                    ->schema([

                                                                        Forms\Components\Placeholder::make('atividades_secundarias')
                                                                            ->label('Atividade Secundárias')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                if ($record->atividades_secundarias) {
                                                                                    $atividades = $record->atividades_secundarias;

                                                                                    $content = '<div class="relative overflow-x-auto"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Descrição</th><th scope="col" class="px-6 py-3">Classe</th></tr></thead><tbody>';
                                                                                    foreach ($atividades as $atividade) {
                                                                                        $content .= '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . $atividade['descricao'] . '</small></th><td class="px-6 py-4">' . $atividade['subclasse'] . '</td></tr>';
                                                                                    }

                                                                                    $content .= '</tbody></table></div>';


                                                                                    return new HtmlString($content);
                                                                                }
                                                                                return new HtmlString('Não definido');
                                                                            }),
                                                                    ]),
                                                            ]),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('Dados de contato')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('company')
                                                            ->schema([

                                                                Forms\Components\Section::make()
                                                                    ->columns(1)
                                                                    ->schema([

                                                                        Forms\Components\Placeholder::make('endereco_atual')
                                                                            ->label('Endereço Atual')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                $tipo = $record['tipo_logradouro'];
                                                                                $numero = $record['numero'];
                                                                                $logradouro = $tipo . ' ' . $record['logradouro'] . ', ' . $numero;
                                                                                $complemento = $record['complemento'];
                                                                                $bairro = $record['bairro'];
                                                                                $cep = $record['cep'];

                                                                                return new HtmlString($logradouro . ' - ' . $complemento . ' - ' . $bairro . ' - ' . $cep);
                                                                            }),

                                                                        Forms\Components\Placeholder::make('telefones')
                                                                            ->label('Telefones')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                $ddd1 = $record['ddd1'];
                                                                                $telefone1 = $record['telefone1'];
                                                                                $ddd2 = $record['ddd2'];
                                                                                $telefone2 = $record['telefone2'];
                                                                                $email = $record['email'];


                                                                                return new HtmlString($ddd1 . ' ' . $telefone1 . ' - ' . $ddd2 . ' ' . $telefone2 . ' - ' . $email);
                                                                            }),

                                                                    ]),
                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Inscrições')
                                                    ->schema([

                                                        Forms\Components\Group::make()
                                                            ->relationship('company')
                                                            ->schema([

                                                                Forms\Components\Section::make()
                                                                    ->columns(1)
                                                                    ->schema([

                                                                        Forms\Components\Placeholder::make('inscricoes_estaduais')
                                                                            ->label('inscricoes_estaduais')
                                                                            ->content(function (ProspectionCompany $record): HtmlString {
                                                                                if ($record->inscricoes_estaduais) {


                                                                                    $inscricoes = $record->inscricoes_estaduais;

                                                                                    $content = '<div class="relative overflow-x-auto"><table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"><thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"><tr><th scope="col" class="px-6 py-3">Ativo?</th><th scope="col" class="px-6 py-3">Inscrição</th></tr></thead><tbody>';
                                                                                    foreach ($inscricoes as $inscricao) {
                                                                                        $ativo = $inscricao['ativo'] ? 'Sim' : 'Não';
                                                                                        $content .= '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . $ativo . '</small></th><td class="px-6 py-4">' . $inscricao['inscricao_estadual'] . ' - ' . $inscricao['estado']['sigla'] . '</td></tr>';
                                                                                    }

                                                                                    $content .= '</tbody></table></div>';


                                                                                    return new HtmlString($content);
                                                                                }
                                                                                return new HtmlString('Não definido');
                                                                            }),
                                                                    ]),
                                                            ]),
                                                    ]),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Atendimento')
                                    ->visibleOn('edit')
                                    ->icon('heroicon-m-chat-bubble-left-right')
                                    ->schema([

                                        Forms\Components\Placeholder::make('notes')
                                            ->label('Anexos')
                                            ->live()
                                            ->content(function (Prospection $record): HtmlString {
                                                $notes = collect($record->annotations);
                                                $notesList = $notes->map(function ($note) {
                                                    return '<span class="text-gray-300">' . Carbon::parse($note['date'])->format('d/m/Y H:i')  . ' - Por: ' . $note['author'] . ' - ' . $note['annotation'] . '</span>';
                                                })->implode('<br>'); // Implode with line break for HTML

                                                return new HtmlString($notesList);
                                            }),

                                        Forms\Components\Repeater::make('annotations')
                                            ->label('Anotações')
                                            ->columns(1)
                                            ->collapsed()
                                            ->addActionLabel('Adicionar anotação')
                                            ->schema([
                                                Forms\Components\Hidden::make('date')
                                                    ->label('Data')
                                                    ->default(now())
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\Hidden::make('author')
                                                    ->label('Autor')
                                                    ->default(auth()->user()->name)
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\RichEditor::make('annotation')
                                                    ->label('Nota')
                                                    ->required()
                                                    ->placeholder('Anotação...'),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Arquivos')
                                    ->icon('heroicon-m-paper-clip')
                                    ->visibleOn('edit')
                                    ->schema([

                                        Forms\Components\Placeholder::make('files')
                                            ->label('Anexos')
                                            ->live()
                                            ->content(function (Prospection $record): HtmlString {
                                                $attachments = collect($record->attachments);
                                                $filesList = $attachments->map(function ($attachment) {
                                                    return '<a href="/storage/' . $attachment['file'] . '" class="text-violet-500 hover:text-violet-700 font-bold uppercase" target="_blank">' . $attachment['title'] . ' - ' . $attachment['file'] . '</a>';
                                                })->implode('<br>'); // Implode with line break for HTML

                                                return new HtmlString($filesList);
                                            }),

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
                                                    ->directory('prospections'),
                                            ]),
                                    ]),

                            ]),
                    ]),

                Forms\Components\Group::make()
                    ->columns(1)
                    ->schema([

                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options(ProspectionStatusEnum::class)
                                    ->default(ProspectionStatusEnum::PENDING)
                                    ->dehydrated()
                                    ->required()
                                    ->disabledOn('create')
                                    ->dehydrated(),
                                Forms\Components\Select::make('reaction')
                                    ->label('Reação')
                                    ->options(ProspectionReactionEnum::class)
                                    ->default(ProspectionReactionEnum::TRYING)
                                    ->required()
                                    ->disabledOn('create')
                                    ->dehydrated(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile.avatar')
                    ->label('Profissional')
                    ->circular()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Descritivo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('process')
                    ->label('Processo')
                    ->badge()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->disabled()
                    ->options(ProspectionStatusEnum::class),
                Tables\Columns\SelectColumn::make('reaction')
                    ->label('Andamento')
                    ->disabled()
                    ->options(ProspectionReactionEnum::class),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListProspections::route('/'),
            'create' => Pages\CreateProspection::route('/create'),
            'edit' => Pages\EditProspection::route('/{record}/edit'),
        ];
    }
}
