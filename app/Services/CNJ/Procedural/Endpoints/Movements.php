<?php

namespace App\Services\CNJ\Procedural\Endpoints;

class Movements extends BaseEndpoint
{
    /**
     * Retrieves the classes associated with a given movement.
     *
     * @param int $movement The ID of the movement.
     * @return array The JSON response containing the classes.
     */
    public function get(int $movement)
    {
        return $this->service->api->get(
            'classes/?movimentos=' . $movement
        )->json();
    }
}
