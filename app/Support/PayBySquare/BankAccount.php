<?php

namespace App\Support\PayBySquare;

class BankAccount
{
    public function __construct(
        private string $iban,
        private ?string $bic = null,
    ) {}

    public function getIban(): string
    {
        return strtoupper(preg_replace('/\s+/', '', $this->iban) ?? '');
    }

    public function getBic(): ?string
    {
        return $this->bic ? strtoupper($this->bic) : null;
    }
}
