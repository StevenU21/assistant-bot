<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RequestLimitExceededException extends Exception
{
    public function __construct($message = "Has alcanzado el límite de peticiones.", $code = 429)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return redirect()->back()->with([
            'message' => $this->getMessage(),
            'status' => 'error'
        ]);
    }
}
