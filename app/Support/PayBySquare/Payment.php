<?php

namespace App\Support\PayBySquare;

class Payment
{
    private float $amount;

    private string $currencyCode = 'EUR';

    private ?\DateTimeInterface $paymentDueDate = null;

    private ?string $variableSymbol = null;

    private ?string $constantSymbol = null;

    private ?string $specificSymbol = null;

    private ?string $reference = null;

    private ?string $paymentNote = null;

    /** @var BankAccount[] */
    private array $bankAccounts = [];

    private ?string $beneficiaryName = null;

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = strtoupper($currencyCode);

        return $this;
    }

    public function setPaymentDueDate(?string $date): self
    {
        if (! filled($date)) {
            $this->paymentDueDate = null;

            return $this;
        }

        $this->paymentDueDate = new \DateTime($date);

        return $this;
    }

    public function setVariableSymbol(?string $variableSymbol): self
    {
        $this->variableSymbol = $variableSymbol;

        return $this;
    }

    public function setPaymentNote(?string $paymentNote): self
    {
        $this->paymentNote = $paymentNote;

        return $this;
    }

    public function addBankAccount(BankAccount $bankAccount): self
    {
        $this->bankAccounts[] = $bankAccount;

        return $this;
    }

    public function setBeneficiaryName(?string $beneficiaryName): self
    {
        $this->beneficiaryName = $beneficiaryName;

        return $this;
    }

    public function toTabDelimitedString(): string
    {
        $paymentData = [
            1,
            number_format($this->amount, 2, '.', ''),
            $this->currencyCode,
            $this->paymentDueDate?->format('Ymd') ?? '',
            $this->variableSymbol ?? '',
            $this->constantSymbol ?? '',
            $this->specificSymbol ?? '',
            $this->reference ?? '',
            $this->paymentNote ?? '',
            count($this->bankAccounts),
        ];

        foreach ($this->bankAccounts as $bankAccount) {
            $paymentData[] = $bankAccount->getIban();
            $paymentData[] = $bankAccount->getBic() ?? '';
        }

        $paymentData[] = '0';
        $paymentData[] = '0';

        if ($this->beneficiaryName !== null) {
            $paymentData[] = $this->beneficiaryName;
        }

        return implode("\t", [
            '',
            '1',
            implode("\t", $paymentData),
        ]);
    }
}
