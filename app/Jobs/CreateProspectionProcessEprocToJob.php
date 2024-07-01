<?php

namespace App\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\ProspectionProcess;
use App\Traits\ProcessNumberParser;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateProspectionProcessEprocToJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ProcessNumberParser;

    public $tries = 5;

    public $backoff = 5;

    public $timeout = 120;

    public function __construct(protected $prospectionID, protected string $process)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $process_parser = $this->processNumberParser($this->process);

        try {
            $url = "https://eproc1.tjto.jus.br/eprocV2_prod_1grau/externo_controlador.php?acao=processo_seleciona_publica&num_processo={$this->process}&eventos=true";

            $client = new Client();
            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();

            $data = $this->parseData($html);

            $response = json_encode($data, JSON_UNESCAPED_UNICODE);
            dd($response);

            ProspectionProcess::create([
                'prospection_id' => $this->prospectionID,
                'process' => $this->process,
                'process_number' => $process_parser['process_number'],
                'process_digit' => $process_parser['process_digit'],
                'process_year' => $process_parser['process_year'],
                'court_code' => $process_parser['court_code'],
                'court_state_code' => $process_parser['court_state_code'],
                'court_district_code' => $process_parser['court_district_code'],
                'classe' => $response['class'],
                'tribunal' => 'TJTO',
                'grau' => 'G1',
                'dataAjuizamento' => Carbon::parse($response['notice_date']),
                'movimentos' => $response['events'],
                'orgaoJulgador' => $response['judging_body'],
                'assuntos' => $response['subjects'],
                'magistrate' => $response['magistrate'],
                'current_situation' => $response['current_situation'],
                'parties_and_representatives' => $response['parties_and_representatives'],
                'additional_information' => $response['additional_information'],
            ]);
        } catch (\Exception $e) {
            $recipient = auth()->user();
            Notification::make()
                ->title('Nenhum processo encontrado')
                ->body($e->getMessage())
                ->sendToDatabase($recipient);
        }
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

        $data['events'] = $this->parseEvents($crawler);

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

    private function parseEvents(Crawler $crawler)
    {
        $events = [];

        // Find the table with the specific headers
        $crawler->filter('table.infraTable')->each(function (Crawler $table) use (&$events) {
            $headers = $table->filter('tr')->first()->filter('th')->each(function (Crawler $node) {
                return trim($node->text());
            });

            // Check if the headers match the expected ones
            if ($headers === ['Evento', 'Data/Hora', 'Descrição', 'Usuário', 'Documentos']) {
                $table->filter('tr')->each(function (Crawler $row, $i) use (&$events) {
                    if ($i > 0) { // Skip the header row
                        $event = [
                            'event' => trim($row->filter('td')->eq(0)->text()),
                            'date_time' => trim($row->filter('td')->eq(1)->text()),
                            'description' => trim($row->filter('td')->eq(2)->html()),
                            'user' => trim($row->filter('td')->eq(3)->text()),
                            'documents' => trim($row->filter('td')->eq(4)->text())
                        ];

                        $event['description'] = preg_replace('/\s+/', ' ', $event['description']); // Replace multiple spaces with single space
                        $event['description'] = html_entity_decode($event['description']); // Decode HTML entities

                        $events[] = $event;
                    }
                });
            }
        });

        return $events;
    }
}
