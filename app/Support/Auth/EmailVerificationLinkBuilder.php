<?php

namespace App\Support\Auth;

use App\Models\User;
use Illuminate\Support\Facades\URL;

class EmailVerificationLinkBuilder
{
    public static function for(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 5)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );
    }
}
