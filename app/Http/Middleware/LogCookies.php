<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogCookies
{
    public function handle(Request $request, Closure $next)
    {
        // log para ver las cookies 
        Log::info('✅ Cookies recibidas:', $request->cookies->all());
        return $next($request);
    }
}
