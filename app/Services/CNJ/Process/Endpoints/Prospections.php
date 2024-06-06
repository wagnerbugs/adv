<?php

namespace App\Services\CNJ\Process\Endpoints;

class Prospections extends BaseEndpoint
{
    public function getProcess(string $url, string $process): array
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
