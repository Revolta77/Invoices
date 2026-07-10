<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invoice_id',
    'position',
    'name',
    'quantity',
    'unit',
    'unit_price',
    'total',
])]
class InvoiceItem extends Model
{
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:4',
            'total' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recalculateTotal(): void
    {
        $this->total = round((float) $this->quantity * (float) $this->unit_price, 2);
    }
}
