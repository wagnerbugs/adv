<?php

namespace App\Services\ZApi\Endpoints;

use App\Services\ZApi\ChatbotService;

trait CanSendMessages
{
    public function messages(): Messages
    {
        return new Messages($this);
    }
}
