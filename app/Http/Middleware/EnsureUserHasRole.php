<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  UserRole|array<int, UserRole|string>  $roles
     */
    public function handle(Request $request, Closure $next, UserRole|string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowedRoles = collect($roles)->map(function (UserRole|string $role) {
            return $role instanceof UserRole ? $role : UserRole::from($role);
        });

        if (! $allowedRoles->contains($user->role)) {
            abort(403, 'Nemáte oprávnenie na prístup k tejto stránke.');
        }

        return $next($request);
    }
}
