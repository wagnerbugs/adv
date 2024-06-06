<?php

namespace App\Services\ApiBrasil\CPF\Endpoints;

trait HasProspections
{
    public function prospections()
    {
        return new Prospections();
    }
}
