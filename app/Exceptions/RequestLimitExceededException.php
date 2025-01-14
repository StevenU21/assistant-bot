<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;

class RequestLimitExceededException extends Exception
{
    public function __construct($message = "Has alcanzado el límite de peticiones.", $code = 429)
    {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        if ($request->header('X-Inertia')) {
            return Redirect::back()->with([
                'message' => $this->getMessage(),
                'status' => 'error'
            ]);
        }

        // Verificar si la solicitud es una llamada AJAX normal
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return new JsonResponse([
                'message' => $this->getMessage(),
                'status' => 'error'
            ], $this->getCode());
        }
    }
}
