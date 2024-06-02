<?php

namespace App\Services\CNJ\Procedural\Endpoints;

class Subjects extends BaseEndpoint
{
    /**
     * Retrieves the details of a subject from the API.
     *
     * @param int $subject The ID of the subject to retrieve.
     * @return array The JSON response from the API containing the details of the subject.
     */
    public function get(int $subject)
    {
        return $this->service->api->get(
            'classes/?assuntos=' . $subject
        )->json();
    }
}
