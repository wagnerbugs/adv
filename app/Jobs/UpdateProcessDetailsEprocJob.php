<?php

namespace App\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Process;
use App\Models\ProcessDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProcessDetailsEprocJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $process)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $process_detail = ProcessDetail::Find($this->process);
        $raw_process_number = $process_detail->process->process;
        $process_number = preg_replace('/[^0-9]/', '', $raw_process_number);

        $url = "https://eproc1.tjto.jus.br/eprocV2_prod_1grau/externo_controlador.php?acao=processo_seleciona_publica&num_processo={$process_number}&eventos=true";

        $client = new Client();
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();

        $data = $this->parseData($html);

        $response = json_encode($data, JSON_UNESCAPED_UNICODE);

        $responseArray = json_decode($response, true);

        // dd($responseArray);

        $process_detail->update([
            'magistrate' => $responseArray['magistrate'],
            'current_situation' => $responseArray['current_situation'],
            'parties_and_representatives' => $responseArray['parties_and_representatives'],
            'additional_information' => $responseArray['additional_information'],
        ]);
    }

    private function parseData($html)
    {
        $crawler = new Crawler($html);

        $data = [];

        $ids = [
            'txtNumProcesso' => 'process',
            'txtAutuacao' => 'notice_date',
            'txtSituacao' => 'current_situation',
            'txtOrgaoJulgador' => 'judging_body',
            'txtMagistrado' => 'magistrate',
            'txtClasse' => 'class',
        ];

        foreach ($ids as $id => $label) {
            $element = $crawler->filter("#{$id}");
            if ($element->count() > 0) {
                $text = trim($element->text());
                $text = html_entity_decode($text);
                $text = preg_replace('/\s+/', ' ', $text); // Replace multiple spaces with single space
                $data[$label] = $text;
            }
        }

        $data['subjects'] = $this->parseSubjects($crawler);

        $data['parties_and_representatives'] = $this->parseParties($crawler);

        $data['additional_information'] = $this->parseAdditionalInformation($crawler);

        // $data['events'] = $this->parseEvents($crawler);

        return $data;
    }

    private function parseSubjects(Crawler $crawler)
    {
        $subjects = [];

        $crawler->filter('#fldAssuntos .infraTrClara')->each(function (Crawler $node) use (&$subjects) {
            $code = trim($node->filter('td')->eq(0)->text());
            $description = trim($node->filter('td')->eq(1)->text());
            $principal = trim($node->filter('td')->eq(2)->text());

            $subject = [
                'code' => $code,
                'description' => html_entity_decode($description),
                'principal' => $principal,
            ];

            $subject['description'] = preg_replace('/\s+/', ' ', $subject['description']);

            $subjects[] = $subject;
        });

        $crawler->filter('#fldAssuntos .infraTrEscura')->each(function (Crawler $node) use (&$subjects) {
            $code = trim($node->filter('td')->eq(0)->text());
            $description = trim($node->filter('td')->eq(1)->text());
            $principal = trim($node->filter('td')->eq(2)->text());

            $subject = [
                'code' => $code,
                'description' => html_entity_decode($description),
                'principal' => $principal,
            ];

            $subject['description'] = preg_replace('/\s+/', ' ', $subject['description']);

            $subjects[] = $subject;
        });

        return $subjects;
    }

    private function parseParties(Crawler $crawler)
    {
        $parties = [
            'first_parties' => [],
            'second_parties' => []
        ];

        $crawler->filter('#fldPartes .infraTrClara')->each(function (Crawler $row, $i) use (&$parties) {
            $columns = $row->filter('td');

            if ($columns->count() == 2) {
                if ($i == 0) {
                    $parties['first_parties'] = $this->parsePartyColumn($columns->eq(0)->html());
                    $parties['second_parties'] = $this->parsePartyColumn($columns->eq(1)->html());
                } else {
                    $parties['second_parties'] = array_merge($parties['second_parties'], $this->parsePartyColumn($columns->eq(1)->html()));
                }
            }
        });

        return $parties;
    }

    private function parsePartyColumn($html)
    {
        // Replace &nbsp; and other entities, then replace multiple spaces with single space
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('&nbsp;', '', $html);
        $parties = array_filter(array_map('trim', explode('<br>', $html)));

        return array_values($parties);
    }

    private function parseAdditionalInformation(Crawler $crawler)
    {
        $additionalInformation = [];
        $crawler->filter('#fldInformacoesAdicionais td')->each(function (Crawler $node, $i) use (&$additionalInformation) {
            if ($node->attr('align') == 'right') {
                $label = trim($node->text());
                $label = rtrim($label, ':'); // Remove the trailing colon
                $valueNode = $node->nextAll()->filter('label.infraLabelObrigatorio');
                if ($valueNode->count() > 0) {
                    $value = trim($valueNode->text());
                    $additionalInformation[$label] = $value;
                }
            }
        });

        return $additionalInformation;
    }
}
