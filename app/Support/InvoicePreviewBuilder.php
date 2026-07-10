<?php

namespace App\Support;

use App\Models\Invoice;
use App\PaymentMethod;
use App\Support\PayBySquare\Generator as PayBySquareGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InvoicePreviewBuilder
{
    /**
     * @return array<string, mixed>
     */
    public static function fromInvoice(Invoice $invoice, bool $forPdf = false): array
    {
        $invoice->loadMissing(['items', 'companyProfile']);

        $profile = $invoice->companyProfile;
        $paymentMethod = $invoice->payment_method ?? PaymentMethod::BankTransfer;
        $isBankTransfer = $paymentMethod === PaymentMethod::BankTransfer;
        $iban = filled($invoice->iban) ? trim($invoice->iban) : null;
        $total = (float) $invoice->total;
        $swift = $isBankTransfer ? SwiftFromIban::guess($iban) : null;

        $logoUrl = self::resolveImagePath($invoice->resolvedLogoUrl());
        $stampUrl = null;

        if ($invoice->signature_enabled) {
            $stampUrl = self::resolveImagePath($invoice->resolvedSignatureUrl());
        }

        $payBySquareQrUrl = null;

        if ($isBankTransfer && filled($iban) && $total > 0) {
            $payload = PayBySquareGenerator::encode([
                'amount' => $total,
                'currency' => $invoice->currency ?? 'EUR',
                'variable_symbol' => $invoice->number,
                'iban' => $iban,
                'swift' => $swift,
                'beneficiary_name' => $profile?->name,
                'note' => __('app.document.pay_note', ['number' => $invoice->number]),
            ]);
            $payBySquareQrUrl = $forPdf
                ? (PayBySquareGenerator::qrImageDataUri($payload) ?? PayBySquareGenerator::qrImageUrl($payload))
                : PayBySquareGenerator::qrImageUrl($payload);
        }

        $items = $invoice->items->map(fn ($row) => [
            'position' => $row->position,
            'name' => $row->name,
            'quantity' => (float) $row->quantity,
            'unit' => $row->unit,
            'unit_price' => (float) $row->unit_price,
            'total' => (float) $row->total,
        ])->all();

        return [
            'number' => $invoice->number,
            'issue_date' => self::formatDate($invoice->issue_date),
            'delivery_date' => self::formatDate($invoice->delivery_date),
            'due_date' => self::formatDate($invoice->due_date),
            'currency' => $invoice->currency ?? 'EUR',
            'iban' => $iban,
            'swift' => $swift,
            'payment_method' => $paymentMethod->label(),
            'payment_method_value' => $paymentMethod->value,
            'is_bank_transfer' => $isBankTransfer,
            'is_identified_person' => (bool) $invoice->is_identified_person,
            'supplier' => [
                'name' => $profile?->name ?? '',
                'street' => $profile?->street ?? '',
                'postal_code' => $profile?->postal_code ?? '',
                'city' => $profile?->city ?? '',
                'country' => $profile?->country ?? 'SK',
                'ico' => $profile?->ico ?? '',
                'dic' => $profile?->dic ?? '',
                'ic_dph' => $profile?->ic_dph ?? '',
                'email' => $profile?->email ?? '',
                'phone' => $profile?->phone ?? '',
                'web' => $profile?->web ?? '',
                'registry' => $profile?->registry ?? '',
                'logo_url' => $logoUrl,
            ],
            'customer' => [
                'name' => $invoice->partner_name ?? '',
                'street' => $invoice->partner_street ?? '',
                'postal_code' => $invoice->partner_postal_code ?? '',
                'city' => $invoice->partner_city ?? '',
                'country' => $invoice->partner_country ?? 'SK',
                'ico' => $invoice->partner_ico ?? '',
                'dic' => $invoice->partner_dic ?? '',
                'ic_dph' => $invoice->partner_ic_dph ?? '',
            ],
            'items' => $items,
            'total' => $total,
            'stamp_url' => $stampUrl,
            'pay_by_square_qr_url' => $payBySquareQrUrl,
        ];
    }

    public static function filename(Invoice $invoice): string
    {
        $safe = preg_replace('/[\\\\\\/:*?"<>|]+/', '-', trim($invoice->number)) ?: 'faktura';

        return "Faktura {$safe}.pdf";
    }

    protected static function formatDate(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof Carbon) {
            return $date->format('d.m.Y');
        }

        try {
            return Carbon::parse($date)->format('d.m.Y');
        } catch (\Throwable) {
            return is_string($date) ? $date : null;
        }
    }

    protected static function resolveImagePath(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        if (str_starts_with($url, '/storage/')) {
            $relative = substr($url, strlen('/storage/'));
            $path = Storage::disk('public')->path($relative);

            return is_file($path) ? $path : public_path(ltrim($url, '/'));
        }

        if (str_starts_with($url, storage_path())) {
            return is_file($url) ? $url : null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $publicPath = public_path(ltrim($url, '/'));

        return is_file($publicPath) ? $publicPath : $url;
    }
}
