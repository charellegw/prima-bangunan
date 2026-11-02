<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $timezone = auth()->user()->store?->timezone ?? Store::first()?->timezone ?? config('app.timezone', 'Asia/Jakarta');

            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
