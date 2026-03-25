<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckStreak
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $request->user()->updateStreak();
        }

        return $next($request);
    }
}