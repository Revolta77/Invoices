<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect()
                ->route('login')
                ->with('verification-error', __('app.auth.verification.link_invalid'));
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('login')
                ->with('status', __('app.auth.verification.already_verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()
            ->route('login')
            ->with('status', __('app.auth.verification.verified_success'));
    }
}
