<?php

namespace App\Exceptions;

use Exception;

class InvalidCpfException extends Exception
{
    protected $message = 'CPF fornecido inválido';
}
