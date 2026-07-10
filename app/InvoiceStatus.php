<?php

namespace App;

enum InvoiceStatus: string
{
    case Paid = 'paid';
    case Unpaid = 'unpaid';
    case Overdue = 'overdue';

    public function label(): string
    {
        return __('app.enums.invoice_status.'.$this->value);
    }
}
