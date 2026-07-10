<?php

namespace App\Support;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ActiveCompanyProfile
{
    public const SESSION_KEY = 'active_company_profile_id';

    public static function id(): ?int
    {
        $id = Session::get(self::SESSION_KEY);

        return is_numeric($id) ? (int) $id : null;
    }

    public static function set(int $profileId): void
    {
        Session::put(self::SESSION_KEY, $profileId);
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function get(): ?CompanyProfile
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $profileId = self::id();

        if ($profileId) {
            $profile = CompanyProfiles::query($user)->find($profileId);

            if ($profile) {
                return $profile;
            }
        }

        return null;
    }

    public static function ensureSelected(User $user): ?CompanyProfile
    {
        $profile = self::get();

        if ($profile) {
            return $profile;
        }

        $first = CompanyProfiles::query($user)->oldest()->first();

        if ($first) {
            self::set($first->id);

            return $first;
        }

        return null;
    }
}
