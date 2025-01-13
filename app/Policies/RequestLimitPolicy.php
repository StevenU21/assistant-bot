<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRequest;
use App\Exceptions\RequestLimitExceededException;

class RequestLimitPolicy
{
    public function makeRequest(User $user)
    {
        $userRequest = UserRequest::where('user_id', $user->id)->first();

        if (!$userRequest || $userRequest->request_count <= 0) {
            throw new RequestLimitExceededException();
        }

        // Decrementar el contador de peticiones
        $userRequest->decrement('request_count');

        return true;
    }
}
