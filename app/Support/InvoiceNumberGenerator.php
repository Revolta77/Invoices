<?php

namespace App\Support;

use App\Models\Invoice;

class InvoiceNumberGenerator
{
    public static function suggest(int $companyProfileId, ?int $year = null): string
    {
        $year ??= (int) date('Y');
        $prefix = (string) $year;

        $existing = Invoice::query()
            ->where('company_profile_id', $companyProfileId)
            ->where('number', 'like', $prefix.'%')
            ->pluck('number');

        $max = 0;

        foreach ($existing as $number) {
            if (! str_starts_with($number, $prefix)) {
                continue;
            }

            $suffix = substr($number, strlen($prefix));

            if ($suffix !== '' && ctype_digit($suffix)) {
                $max = max($max, (int) $suffix);
            }
        }

        $next = $max + 1;

        return $prefix.str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }

    public static function isAvailable(int $companyProfileId, string $number, ?int $ignoreInvoiceId = null): bool
    {
        return ! Invoice::query()
            ->where('company_profile_id', $companyProfileId)
            ->where('number', $number)
            ->when($ignoreInvoiceId, fn ($q) => $q->where('id', '!=', $ignoreInvoiceId))
            ->exists();
    }
}
