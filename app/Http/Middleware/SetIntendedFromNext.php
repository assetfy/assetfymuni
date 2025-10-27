<?php

// App\Http\Middleware\SetIntendedFromNext.php
namespace App\Http\Middleware;

use Closure;

class SetIntendedFromNext
{
    public function handle($request, Closure $next)
    {
        if ($request->has('next') && $this->isSafeLocalUrl($request->input('next'))) {
            session(['url.intended' => $request->input('next')]);
        }
        return $next($request);
    }

    private function isSafeLocalUrl(string $url): bool
    {
        $app = parse_url(config('app.url'));
        $to  = parse_url($url);
        return !isset($to['host']) || (isset($app['host'], $to['host']) && $to['host'] === $app['host']);
    }
}
