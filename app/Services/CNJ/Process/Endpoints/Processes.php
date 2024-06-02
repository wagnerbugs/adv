<?php

namespace App\Services\CNJ\Process\Endpoints;

class Processes extends BaseEndpoint
{
    /**
     * Retrieves a process from the API using the given URL and process number.
     *
     * @param string $url The URL of the API endpoint.
     * @param string $process The process number to search for.
     * @return mixed The JSON response from the API.
     */
    public function getProcess(string $url, string $process)
    {
        return $this->service->api->post(
            $url . '/_search',
            [
                'query' => [
                    'match' => [
                        'numeroProcesso' => $process
                    ]
                ]
            ]
        )->json();
    }
}
