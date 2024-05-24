<?php

namespace App\Services\CnpjWs\Endpoints;

trait HasCompanies
{
    public function companies()
    {
        return new Companies();
    }
}
