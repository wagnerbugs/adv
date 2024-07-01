<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class PublicData extends Command
{
    protected $signature = 'public';

    protected $description = 'Command description';

    public function handle()
    {
        //00035007120248272729
        try {
            $url = 'https://eproc1.tjto.jus.br/eprocV2_prod_1grau/externo_controlador.php?acao=processo_seleciona_publica&num_processo=00210629320248272729&eventos=true';

            $client = new Client();
            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();

            $data = $this->parseData($html);

            $response = json_encode($data, JSON_UNESCAPED_UNICODE);
            dd($response);
        } catch (\Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
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
