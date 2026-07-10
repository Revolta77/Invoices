<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\GoogleDriveBackupDispatcher;
use App\Support\GoogleOAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLinkController extends Controller
{
    public function redirect(): RedirectResponse
    {
        abort_unless(auth()->check(), 403);

        session(['google_link_intent' => true]);

        /** @var User $user */
        $user = auth()->user();

        return GoogleOAuth::driver(
            forceConsent: ! filled($user->google_refresh_token),
            redirectUrl: route('auth.google.link.callback')
        )->redirect();
    }

    public function linkCallback(): RedirectResponse
    {
        abort_unless(auth()->check(), 403);

        if (! session()->pull('google_link_intent')) {
            return redirect()->route('dashboard');
        }

        $googleUser = Socialite::driver('google')
            ->redirectUrl(route('auth.google.link.callback'))
            ->user();

        /** @var User $currentUser */
        $currentUser = auth()->user();

        $existing = User::query()
            ->where('google_id', $googleUser->getId())
            ->where('id', '!=', $currentUser->id)
            ->exists();

        if ($existing) {
            return redirect()->route('dashboard', ['view' => 'settings'])
                ->with('settings-status', __('app.messages.google_link_already_used'));
        }

        $currentUser->update([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ]);

        GoogleOAuth::storeTokens($currentUser, $googleUser);

        GoogleDriveBackupDispatcher::dispatch($currentUser);

        return redirect()->route('dashboard', ['view' => 'settings'])
            ->with('settings-status', __('app.messages.google_linked'));
    }
}
