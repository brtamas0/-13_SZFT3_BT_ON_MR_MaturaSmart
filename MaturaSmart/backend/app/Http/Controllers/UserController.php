<?php

namespace App\Http\Controllers;


use App\Models\GlobalMessage;
use Illuminate\Http\Request;

class UserController extends Controller
{

public function getGlobalMessage()
{
    $message = GlobalMessage::where('is_active', true)
        ->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->latest()
        ->first();

    return response()->json($message);
}
}
