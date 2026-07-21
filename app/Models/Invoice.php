<?php

namespace App\Models;

use App\InvoiceStatus;
use App\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'company_profile_id',
    'user_id',
    'number',
    'partner_name',
    'partner_ico',
    'partner_street',
    'partner_postal_code',
    'partner_city',
    'partner_country',
    'partner_dic',
    'partner_ic_dph',
    'issue_date',
    'delivery_date',
    'due_date',
    'due_days',
    'is_identified_person',
    'currency',
    'exchange_rate',
    'iban',
    'bank_account',
    'payment_method',
    'status',
    'paid_at',
    'emailed_at',
    'paid_amount',
    'paid_payment_method',
    'is_locked',
    'locked_at',
    'total',
    'signature_enabled',
    'signature_path',
    'signature_text',
    'logo_enabled',
    'logo_path',
])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'delivery_date' => 'date',
            'due_date' => 'date',
            'due_days' => 'integer',
            'is_identified_person' => 'boolean',
            'exchange_rate' => 'decimal:4',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'emailed_at' => 'datetime',
            'paid_amount' => 'decimal:2',
            'signature_enabled' => 'boolean',
            'logo_enabled' => 'boolean',
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'status' => InvoiceStatus::class,
            'payment_method' => PaymentMethod::class,
            'paid_payment_method' => PaymentMethod::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice) {
            $invoice->refreshStatus();
        });
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('position');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(InvoiceEmailLog::class)->latest('sent_at');
    }

    public function latestEmailLog(): HasOne
    {
        return $this->hasOne(InvoiceEmailLog::class)->latestOfMany('sent_at');
    }

    public function refreshStatus(): void
    {
        if ($this->paid_at !== null || $this->status === InvoiceStatus::Paid) {
            $this->status = InvoiceStatus::Paid;

            return;
        }

        if ($this->due_date && $this->due_date->copy()->endOfDay()->isPast()) {
            $this->status = InvoiceStatus::Overdue;

            return;
        }

        $this->status = InvoiceStatus::Unpaid;
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->items()->sum('total');
    }

    public function lock(?\Illuminate\Support\Carbon $lockedAt = null): void
    {
        $this->forceFill([
            'is_locked' => true,
            'locked_at' => $lockedAt ?? now(),
        ])->save();
    }

    public function unlock(): void
    {
        $this->forceFill([
            'is_locked' => false,
            'locked_at' => null,
        ])->save();
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    public function signatureUrl(): ?string
    {
        return $this->signature_path ? Storage::disk('public')->url($this->signature_path) : null;
    }

    public function resolvedSignatureUrl(): ?string
    {
        return $this->signatureUrl() ?? $this->companyProfile?->stampUrl();
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function resolvedLogoUrl(): ?string
    {
        if (! $this->logo_enabled) {
            return null;
        }

        return $this->logoUrl() ?? $this->companyProfile?->logoUrl();
    }
}
