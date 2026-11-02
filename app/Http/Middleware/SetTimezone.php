<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {

            $timezone = auth()->user()->store?->timezone ?? config('app.timezone');

            config([
                'app.timezone' => $timezone
            ]);

            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}