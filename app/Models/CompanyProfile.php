<?php

namespace App\Models;

use App\TaxpayerType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'name',
    'street',
    'postal_code',
    'city',
    'country',
    'ico',
    'dic',
    'taxpayer_type',
    'ic_dph',
    'registry',
    'email',
    'phone',
    'web',
    'logo_path',
    'stamp_path',
])]
class CompanyProfile extends Model
{
    protected function casts(): array
    {
        return [
            'taxpayer_type' => TaxpayerType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function stampUrl(): ?string
    {
        return $this->stamp_path ? Storage::disk('public')->url($this->stamp_path) : null;
    }

    public function toFormArray(): array
    {
        return [
            'name' => $this->name,
            'street' => $this->street ?? '',
            'postal_code' => $this->postal_code ?? '',
            'city' => $this->city ?? '',
            'country' => $this->country ?? 'SK',
            'ico' => $this->ico ?? '',
            'dic' => $this->dic ?? '',
            'taxpayer_type' => $this->taxpayer_type?->value ?? TaxpayerType::NeplatitelDph->value,
            'ic_dph' => $this->ic_dph ?? '',
            'registry' => $this->registry ?? '',
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'web' => $this->web ?? '',
        ];
    }
}
