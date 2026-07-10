<?php

namespace App\Support;

use App\Jobs\SyncGoogleDriveBackupJob;
use App\Models\User;

class GoogleDriveBackupDispatcher
{
    public static function dispatch(?User $user = null): void
    {
        if (! config('google-drive.backup_enabled', true)) {
            return;
        }

        $user ??= auth()->user();

        if (! $user instanceof User || ! $user->canSyncToGoogleDrive()) {
            return;
        }

        SyncGoogleDriveBackupJob::dispatch($user->id);
    }
}
