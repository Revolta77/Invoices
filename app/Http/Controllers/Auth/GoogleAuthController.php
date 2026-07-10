<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\GoogleDriveBackupDispatcher;
use App\Support\GoogleOAuth;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return GoogleOAuth::driver(
            forceConsent: true,
            redirectUrl: route('auth.google.callback')
        )->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')
            ->redirectUrl(route('auth.google.callback'))
            ->user();

        $user = User::query()->where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::query()->where('email', $googleUser->getEmail())->first();
            $isNewUser = false;

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $isNewUser = true;

                $user = User::query()->create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'role' => UserRole::User,
                ]);
            }
        } else {
            $isNewUser = false;
        }

        GoogleOAuth::storeTokens($user, $googleUser);

        Auth::login($user, remember: true);

        if ($isNewUser ?? false) {
            $user->sendWelcomeNotification();
        }

        GoogleDriveBackupDispatcher::dispatch($user);

        return redirect()->intended(route('dashboard'));
    }
}
