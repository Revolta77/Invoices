<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('locales.available', []));

        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale');
        }

        if (! in_array($locale, $available, true) && $available !== []) {
            $locale = $available[0];
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
