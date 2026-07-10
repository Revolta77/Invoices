<?php

namespace App\Support;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuth
{
    public const DRIVE_FILE_SCOPE = 'https://www.googleapis.com/auth/drive.file';

    public static function driver(bool $forceConsent = false, ?string $redirectUrl = null)
    {
        $driver = Socialite::driver('google')
            ->scopes([
                'openid',
                'email',
                'profile',
                self::DRIVE_FILE_SCOPE,
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => $forceConsent ? 'consent' : 'select_account',
            ]);

        if ($redirectUrl !== null) {
            $driver->redirectUrl($redirectUrl);
        }

        return $driver;
    }

    public static function storeTokens(User $user, SocialiteUser $googleUser): void
    {
        $update = [];

        if (filled($googleUser->token ?? null)) {
            $update['google_access_token'] = $googleUser->token;
        }

        if (filled($googleUser->refreshToken ?? null)) {
            $update['google_refresh_token'] = $googleUser->refreshToken;
        }

        if (filled($googleUser->expiresIn ?? null)) {
            $update['google_token_expires_at'] = now()->addSeconds((int) $googleUser->expiresIn);
        }

        if ($update !== []) {
            $user->update($update);
        }
    }
}
