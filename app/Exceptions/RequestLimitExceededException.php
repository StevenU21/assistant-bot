<?php

namespace App\Exceptions;

use Exception;

class RequestLimitExceededException extends Exception
{
    public function __construct($message = "Has alcanzado el límite de peticiones.", $code = 429)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return response()->json(['error' => $this->getMessage()], $this->getCode());
    }
}
