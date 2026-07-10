<?php

namespace App\Http\Middleware;

use App\Support\ActiveCompanyProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyProfile
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs([
            'company-profiles.create',
            'company-profiles.edit',
            'settings',
            'auth.google.redirect',
            'auth.google.callback',
        ])) {
            return $next($request);
        }

        if (CompanyProfiles::query($user)->doesntExist()) {
            return redirect()->route('company-profiles.create');
        }

        ActiveCompanyProfile::ensureSelected($user);

        return $next($request);
    }
}
