<?php

namespace App\Services\ZApi\Endpoints;

/**
 * Trait CanSendMessages
 *
 * Provides message sending capabilities.
 */
trait CanSendMessages
{
    /**
     * Get the Messages endpoint instance.
     *
     * @return Messages
     */
    public function messages(): Messages
    {
        return new Messages($this->service);
    }
}
