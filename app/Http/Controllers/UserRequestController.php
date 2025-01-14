<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRequestController extends Controller
{
    public function updateRequestCount(Request $request)
    {
        $user = Auth::user();
        $userRequest = $user->user_request;
        $userRequest->request_count = 10;
        $userRequest->save();

        return response()->json(['message' => 'Request count updated successfully']);
    }

    public function getRequestCount()
    {
        $user = Auth::user();
        $userRequest = $user->user_request;

        return response()->json(['request_count' => $userRequest->request_count]);
    }
}
