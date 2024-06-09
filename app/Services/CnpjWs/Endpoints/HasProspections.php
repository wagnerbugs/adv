<?php

namespace App\Services\CnpjWs\Endpoints;

use App\Services\CnpjWs\Endpoints\Prospections;

trait HasProspections
{
    public function prospections()
    {
        return new Prospections();
    }
}
