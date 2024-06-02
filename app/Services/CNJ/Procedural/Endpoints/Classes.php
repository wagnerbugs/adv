<?php

namespace App\Services\CNJ\Procedural\Endpoints;

class Classes extends BaseEndpoint
{
    /**
     * Retrieves the details of a class from the API.
     *
     * @param int $class The ID of the class to retrieve.
     * @return array The JSON response from the API containing the details of the class.
     */
    public function get(int $class)
    {
        return $this->service->api->get(
            'classes/?codigo=' . $class
        )->json();
    }
}
