<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class LockOldInvoices extends Command
{
    protected $signature = 'invoices:lock-old';

    protected $description = 'Lock invoices older than two months';

    public function handle(): int
    {
        $cutoff = now()->startOfMonth()->subMonths(1);
        $lockedAt = now();

        $lockedCount = Invoice::query()
            ->where('is_locked', false)
            ->whereDate('issue_date', '<', $cutoff->toDateString())
            ->update([
                'is_locked' => true,
                'locked_at' => $lockedAt,
                'updated_at' => $lockedAt,
            ]);

        $this->info("Locked {$lockedCount} invoice(s) issued before {$cutoff->toDateString()}.");

        return self::SUCCESS;
    }
}
