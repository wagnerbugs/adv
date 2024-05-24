<?php

namespace App\Exceptions;

use Exception;

class ApiBrasilErrorResponseException extends Exception
{
    protected $data;

    public function __construct($message = 'API returned an error', $data = [], $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
