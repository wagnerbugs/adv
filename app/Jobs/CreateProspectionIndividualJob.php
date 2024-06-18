<?php

namespace App\Jobs;

use App\Models\Prospection;
use App\Services\ApiBrasil\CPF\ApiBrasilCPFService;
use App\Traits\CapitalizeTrait;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CreateProspectionIndividualJob implements ShouldQueue
{
    use Batchable, CapitalizeTrait, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $backoff = 5;

    public $timeout = 120;

    public function __construct(protected Prospection $prospection, protected string $cpf)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new ApiBrasilCPFService();
        $response = $service->prospections()->getCPF($this->cpf);

        $responseContent = $response['response']['content'];
        $nomeConteudo = $responseContent['nome']['conteudo'];

        if (! empty($nomeConteudo['data_nascimento'])) {
            $birth_date = Carbon::createFromFormat('d/m/Y', $nomeConteudo['data_nascimento']);
            $formatted_date = $birth_date->format('Y-m-d');
        }

        $this->prospection->individual()->update([
            'cpf' => $this->cpf,
            'mae' => $this->capitalize($nomeConteudo['mae']),
            'tipo_documento' => $nomeConteudo['tipo_documento'],
            'nome' => $this->capitalize($nomeConteudo['nome']),
            'outras_grafias' => json_encode($nomeConteudo['outras_grafias'], true),
            'data_nascimento' => $formatted_date,
            'outras_datas_nascimento' => json_encode($nomeConteudo['outras_datas_nascimento'], true),
            'pessoa_exposta_publicamente' => json_encode($nomeConteudo['pessoa_exposta_publicamente'], true),
            'idade' => $nomeConteudo['idade'],
            'signo' => $nomeConteudo['signo'],
            'obito' => $nomeConteudo['obito'],
            'data_obito' => $nomeConteudo['data_obito'],
            'sexo' => $nomeConteudo['sexo'],
            'uf' => $nomeConteudo['uf'],
            'situacao_receita' => $nomeConteudo['situacao_receita'],
            'situacao_receita_data' => $nomeConteudo['situacao_receita_data'],
            'situacao_receita_hora' => $nomeConteudo['situacao_receita_hora'],
            'dados_parentes' => json_encode($responseContent['dados_parentes']['conteudo'], true),
            'pessoas_contato' => json_encode($responseContent['pessoas_contato']['conteudo'], true),
            'pesquisa_enderecos' => json_encode($responseContent['pesquisa_enderecos']['conteudo'], true),
            'trabalha_trabalhou' => json_encode($responseContent['trabalha_trabalhou']['conteudo'], true),
            'contato_preferencial' => json_encode($responseContent['contato_preferencial']['conteudo'], true),
            'residentes_mesmo_domicilio' => json_encode($responseContent['residentes_mesmo_domicilio']['conteudo'], true),
            'emails' => json_encode($responseContent['emails']['conteudo'], true),
            'numero_beneficio' => json_encode($responseContent['numero_beneficio'], true),
            'alerta_participacoes' => json_encode($responseContent['alerta_participacoes']['conteudo'], true),
            'pesquisa_telefones_fixo' => json_encode($responseContent['pesquisa_telefones']['conteudo']['fixo'], true),
            'pesquisa_telefones_celular' => json_encode($responseContent['pesquisa_telefones']['conteudo']['celular'], true),
            'alerta_monitore' => json_encode($responseContent['alerta_monitore'], true),
            'outros_documentos' => json_encode($responseContent['outros_documentos'], true),
            'protocolo' => $responseContent['protocolo'],
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title($exception->getMessage())
            ->sendToDatabase($recipient);
    }
}
