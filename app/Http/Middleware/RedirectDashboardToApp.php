<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectDashboardToApp
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('dashboard')) {
            $query = $request->getQueryString();
            $target = '/app'.($query ? '?'.$query : '');

            return redirect($target);
        }

        return $next($request);
    }
}
