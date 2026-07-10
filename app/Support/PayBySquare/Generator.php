<?php

namespace App\Support\PayBySquare;

use App\Support\SwiftFromIban;
use Illuminate\Support\Facades\Http;

class Generator
{
    public static function encode(array $params): ?string
    {
        $iban = $params['iban'] ?? null;
        $amount = (float) ($params['amount'] ?? 0);

        if (! filled($iban) || $amount <= 0) {
            return null;
        }

        $payment = (new Payment())
            ->setAmount($amount)
            ->setCurrencyCode($params['currency'] ?? 'EUR')
            ->setVariableSymbol($params['variable_symbol'] ?? null)
            ->setPaymentNote($params['note'] ?? null)
            ->setBeneficiaryName($params['beneficiary_name'] ?? null);

        $payment->addBankAccount(new BankAccount(
            $iban,
            $params['swift'] ?? SwiftFromIban::guess($iban),
        ));

        return (new Encoder())->encode($payment);
    }

    public static function qrImageUrl(?string $payload, int $size = 140): ?string
    {
        if (! filled($payload)) {
            return null;
        }

        return 'https://api.qrserver.com/v1/create-qr-code/?'.http_build_query([
            'size' => "{$size}x{$size}",
            'data' => $payload,
            'ecc' => 'M',
            'margin' => 2,
            'color' => '0c7a61',
            'bgcolor' => 'ffffff',
        ]);
    }

    public static function qrImageDataUri(?string $payload, int $size = 140): ?string
    {
        $url = self::qrImageUrl($payload, $size);

        if (! filled($url)) {
            return null;
        }

        try {
            $response = Http::timeout(8)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $mime = $response->header('Content-Type') ?: 'image/png';

            return 'data:'.$mime.';base64,'.base64_encode($response->body());
        } catch (\Throwable) {
            return null;
        }
    }
}
