<?php

namespace App\Support;

class SwiftFromIban
{
  /**
   * @var array<string, string>
   */
    private const SK_BANK_CODES = [
        '0200' => 'SUBASKBX',
        '0900' => 'GIBASKBX',
        '1100' => 'TATRSKBX',
        '1111' => 'UNCRSKBX',
        '3000' => 'SLZBSKBA',
        '3100' => 'LUBASKBX',
        '5200' => 'OTPVSKBX',
        '5600' => 'KOMASK2X',
        '6500' => 'POBNSKBA',
        '7500' => 'CEKOSKBX',
        '7930' => 'WUSTSKBA',
        '8130' => 'CITISKBA',
        '8170' => 'KOMBSKBA',
        '8330' => 'FIOZSKBA',
        '8360' => 'BREXSKBX',
    ];

    public static function guess(?string $iban): ?string
    {
        if (! filled($iban)) {
            return null;
        }

        $normalized = strtoupper(preg_replace('/\s+/', '', $iban) ?? '');

        if (! str_starts_with($normalized, 'SK') || strlen($normalized) < 8) {
            return null;
        }

        $bankCode = substr($normalized, 4, 4);

        return self::SK_BANK_CODES[$bankCode] ?? null;
    }
}
