<?php

namespace App\Support;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CompanyProfiles
{
    /**
     * @return Builder<CompanyProfile>
     */
    public static function query(?User $user = null): Builder
    {
        $user ??= auth()->user();

        return CompanyProfile::query()->where('user_id', $user->id);
    }

    public static function exists(?User $user = null): bool
    {
        return self::query($user)->exists();
    }

    public static function count(?User $user = null): int
    {
        return self::query($user)->count();
    }
}
