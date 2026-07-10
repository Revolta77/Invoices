<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GoogleDriveBackupExporter;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncGoogleDriveBackupJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 120;

    public function __construct(public int $userId) {}

    public function uniqueId(): string
    {
        return 'google-drive-backup-'.$this->userId;
    }

    public function handle(GoogleDriveBackupExporter $exporter): void
    {
        $user = User::query()->find($this->userId);

        if (! $user || ! $user->canSyncToGoogleDrive()) {
            return;
        }

        $user->update([
            'google_backup_status' => 'syncing',
            'google_backup_error' => null,
        ]);

        try {
            $exporter->export($user);

            $user->update([
                'google_backup_status' => 'success',
                'google_backup_last_at' => now(),
                'google_backup_error' => null,
            ]);
        } catch (Throwable $exception) {
            $user->update([
                'google_backup_status' => 'failed',
                'google_backup_error' => $exception->getMessage(),
            ]);

            report($exception);
        }
    }
}
