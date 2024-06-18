<?php

namespace App\Services\CNJ\Process\Endpoints;

use App\Services\CNJ\Process\Entities\Process;

class Processes extends BaseEndpoint
{
    /**
     * Retrieves a process from the API using the given URL and process number.
     *
     * @param  string  $url  The URL of the API endpoint.
     * @param  string  $process  The process number to search for.
     * @return Process[] An array of Process instances.
     */
    public function getProcess(string $url, string $process)
    {
        $response = $this->service->api->post(
            $url.'/_search',
            [
                'query' => [
                    'match' => [
                        'numeroProcesso' => $process,
                    ],
                ],
            ]
        )->json();

        if (data_get($response, 'hits.total.value') === 0) {
            return ['error' => 'No processes found for the given number.'];
        }

        $hits = data_get($response, 'hits.hits', []);

        return array_map(function ($hit) {
            return new Process($hit);
        }, $hits);
    }
}
