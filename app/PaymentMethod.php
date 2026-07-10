<?php

namespace App;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return __('app.enums.payment_method.'.$this->value);
    }
}
