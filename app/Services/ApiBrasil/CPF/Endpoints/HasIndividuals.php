<?php

namespace App\Services\ApiBrasil\CPF\Endpoints;

trait HasIndividuals
{
    public function individuals()
    {
        return new Individuals();
    }
}
