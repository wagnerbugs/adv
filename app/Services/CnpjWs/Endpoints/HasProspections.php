<?php

namespace App\Services\CnpjWs\Endpoints;

trait HasProspections
{
    public function prospections()
    {
        return new Prospections();
    }
}
