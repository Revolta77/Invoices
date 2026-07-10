<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\InvoiceEmailLog;
use App\Models\InvoiceItem;
use App\Models\User;
use App\PaymentMethod;
use App\Support\GoogleDrive\GoogleDriveClient;
use App\InvoiceStatus;
use App\TaxpayerType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GoogleDriveBackupImporter
{
    public function import(User $user): array
    {
        if (! $user->canSyncToGoogleDrive()) {
            throw new \RuntimeException('Google Drive import nie je dostupný. Prepojte Google účet.');
        }

        $client = new GoogleDriveClient($user);
        $rootFolderId = $client->findOrCreateRootFolder();
        $backupFilename = (string) config('google-drive.backup_filename', 'backup.json');
        $backupFile = $client->findFileByName($backupFilename, $rootFolderId);

        if ($backupFile === null) {
            throw new \RuntimeException('Na Google Drive sa nenašiel súbor backup.json.');
        }

        $payload = json_decode($client->downloadFile($backupFile['id']), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($payload)) {
            throw new \RuntimeException('Súbor backup.json má neplatný formát.');
        }

        return DB::transaction(function () use ($user, $client, $rootFolderId, $payload) {
            $profileMap = [];
            $importedProfiles = 0;
            $importedInvoices = 0;

            foreach ($payload['company_profiles'] ?? [] as $profileData) {
                $profile = $this->importProfile($user, $profileData);
                $profileMap[(int) ($profileData['backup_id'] ?? $profile->id)] = $profile->id;
                $this->restoreProfileAssets($client, $rootFolderId, $profile, $profileData);
                $importedProfiles++;
            }

            foreach ($payload['invoices'] ?? [] as $invoiceData) {
                $profileId = $profileMap[(int) ($invoiceData['company_profile_backup_id'] ?? 0)] ?? null;

                if ($profileId === null) {
                    continue;
                }

                $invoice = $this->importInvoice($user, $profileId, $invoiceData);
                $this->restoreInvoiceAssets($client, $rootFolderId, $invoice, $invoiceData);
                $importedInvoices++;
            }

            return [
                'profiles' => $importedProfiles,
                'invoices' => $importedInvoices,
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $profileData
     */
    private function importProfile(User $user, array $profileData): CompanyProfile
    {
        $query = CompanyProfile::query()->where('user_id', $user->id);

        $profile = null;

        if (filled($profileData['ico'] ?? null)) {
            $profile = (clone $query)->where('ico', $profileData['ico'])->first();
        }

        if ($profile === null && filled($profileData['name'] ?? null)) {
            $profile = (clone $query)->where('name', $profileData['name'])->first();
        }

        $attributes = [
            'user_id' => $user->id,
            'name' => (string) ($profileData['name'] ?? 'Profil'),
            'street' => $profileData['street'] ?? null,
            'postal_code' => $profileData['postal_code'] ?? null,
            'city' => $profileData['city'] ?? null,
            'country' => $profileData['country'] ?? 'SK',
            'ico' => $profileData['ico'] ?? null,
            'dic' => $profileData['dic'] ?? null,
            'taxpayer_type' => TaxpayerType::tryFrom((string) ($profileData['taxpayer_type'] ?? ''))
                ?? TaxpayerType::NeplatitelDph,
            'ic_dph' => $profileData['ic_dph'] ?? null,
            'registry' => $profileData['registry'] ?? null,
            'email' => $profileData['email'] ?? null,
            'phone' => $profileData['phone'] ?? null,
            'web' => $profileData['web'] ?? null,
        ];

        if ($profile) {
            $profile->update($attributes);

            return $profile->fresh();
        }

        return CompanyProfile::query()->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $invoiceData
     */
    private function importInvoice(User $user, int $profileId, array $invoiceData): Invoice
    {
        $invoice = Invoice::query()
            ->where('user_id', $user->id)
            ->where('company_profile_id', $profileId)
            ->where('number', $invoiceData['number'] ?? '')
            ->first();

        $attributes = [
            'company_profile_id' => $profileId,
            'user_id' => $user->id,
            'number' => (string) ($invoiceData['number'] ?? ''),
            'partner_name' => (string) ($invoiceData['partner_name'] ?? ''),
            'partner_ico' => $invoiceData['partner_ico'] ?? null,
            'partner_street' => $invoiceData['partner_street'] ?? null,
            'partner_postal_code' => $invoiceData['partner_postal_code'] ?? null,
            'partner_city' => $invoiceData['partner_city'] ?? null,
            'partner_country' => $invoiceData['partner_country'] ?? 'SK',
            'partner_dic' => $invoiceData['partner_dic'] ?? null,
            'partner_ic_dph' => $invoiceData['partner_ic_dph'] ?? null,
            'issue_date' => $invoiceData['issue_date'] ?? now()->toDateString(),
            'delivery_date' => $invoiceData['delivery_date'] ?? null,
            'due_date' => $invoiceData['due_date'] ?? null,
            'due_days' => $invoiceData['due_days'] ?? null,
            'is_identified_person' => (bool) ($invoiceData['is_identified_person'] ?? false),
            'currency' => $invoiceData['currency'] ?? 'EUR',
            'exchange_rate' => $invoiceData['exchange_rate'] ?? null,
            'iban' => $invoiceData['iban'] ?? null,
            'bank_account' => $invoiceData['bank_account'] ?? null,
            'payment_method' => PaymentMethod::tryFrom((string) ($invoiceData['payment_method'] ?? ''))
                ?? PaymentMethod::BankTransfer,
            'status' => InvoiceStatus::tryFrom((string) ($invoiceData['status'] ?? ''))
                ?? InvoiceStatus::Unpaid,
            'paid_at' => filled($invoiceData['paid_at'] ?? null) ? Carbon::parse($invoiceData['paid_at']) : null,
            'emailed_at' => filled($invoiceData['emailed_at'] ?? null) ? Carbon::parse($invoiceData['emailed_at']) : null,
            'paid_amount' => $invoiceData['paid_amount'] ?? null,
            'paid_payment_method' => PaymentMethod::tryFrom((string) ($invoiceData['paid_payment_method'] ?? '')),
            'total' => $invoiceData['total'] ?? 0,
            'signature_enabled' => (bool) ($invoiceData['signature_enabled'] ?? true),
            'signature_text' => $invoiceData['signature_text'] ?? null,
            'logo_enabled' => (bool) ($invoiceData['logo_enabled'] ?? false),
        ];

        if ($invoice) {
            $invoice->update($attributes);
        } else {
            $invoice = Invoice::query()->create($attributes);
        }

        $invoice->items()->delete();

        foreach ($invoiceData['items'] ?? [] as $itemData) {
            InvoiceItem::query()->create([
                'invoice_id' => $invoice->id,
                'position' => (int) ($itemData['position'] ?? 1),
                'name' => (string) ($itemData['name'] ?? ''),
                'quantity' => $itemData['quantity'] ?? 1,
                'unit' => $itemData['unit'] ?? 'ks',
                'unit_price' => $itemData['unit_price'] ?? 0,
                'total' => $itemData['total'] ?? 0,
            ]);
        }

        $invoice->recalculateTotal();
        $invoice->refreshStatus();
        $invoice->save();

        $invoice->emailLogs()->delete();

        foreach ($invoiceData['email_logs'] ?? [] as $logData) {
            InvoiceEmailLog::query()->create([
                'invoice_id' => $invoice->id,
                'company_profile_id' => $profileId,
                'user_id' => $user->id,
                'partner_ico' => $logData['partner_ico'] ?? null,
                'partner_name' => (string) ($logData['partner_name'] ?? $invoice->partner_name),
                'to_email' => (string) ($logData['to_email'] ?? ''),
                'cc_email' => $logData['cc_email'] ?? null,
                'from_email' => (string) ($logData['from_email'] ?? ''),
                'subject' => (string) ($logData['subject'] ?? ''),
                'sent_at' => filled($logData['sent_at'] ?? null) ? Carbon::parse($logData['sent_at']) : now(),
            ]);
        }

        return $invoice->fresh(['items', 'emailLogs']);
    }

    /**
     * @param  array<string, mixed>  $profileData
     */
    private function restoreProfileAssets(
        GoogleDriveClient $client,
        string $rootFolderId,
        CompanyProfile $profile,
        array $profileData
    ): void {
        $backupId = (int) ($profileData['backup_id'] ?? $profile->id);
        $assetsFolderId = $client->findOrCreateFolder('assets', $rootFolderId);
        $profilesFolderId = $client->findOrCreateFolder('profiles', $assetsFolderId);
        $profileFolderId = $client->findOrCreateFolder((string) $backupId, $profilesFolderId);

        if (filled($profileData['assets']['logo'] ?? null)) {
            $filename = basename((string) $profileData['assets']['logo']);
            $this->restoreAssetFile($client, $profileFolderId, $filename, 'company-profiles/'.$profile->id.'/logo.'.$this->extension($filename), 'logo_path', $profile);
        }

        if (filled($profileData['assets']['stamp'] ?? null)) {
            $filename = basename((string) $profileData['assets']['stamp']);
            $this->restoreAssetFile($client, $profileFolderId, $filename, 'company-profiles/'.$profile->id.'/stamp.'.$this->extension($filename), 'stamp_path', $profile);
        }
    }

    /**
     * @param  array<string, mixed>  $invoiceData
     */
    private function restoreInvoiceAssets(
        GoogleDriveClient $client,
        string $rootFolderId,
        Invoice $invoice,
        array $invoiceData
    ): void {
        $backupId = (int) ($invoiceData['backup_id'] ?? $invoice->id);
        $assetsFolderId = $client->findOrCreateFolder('assets', $rootFolderId);
        $invoicesFolderId = $client->findOrCreateFolder('invoices', $assetsFolderId);
        $invoiceFolderId = $client->findOrCreateFolder((string) $backupId, $invoicesFolderId);

        foreach (['signature' => 'signature_path', 'logo' => 'logo_path'] as $prefix => $column) {
            $file = $client->findFileByName($prefix.'.png', $invoiceFolderId)
                ?? $client->findFileByName($prefix.'.jpg', $invoiceFolderId);

            if ($file === null) {
                continue;
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'png';
            $targetPath = 'invoices/'.$invoice->id.'/'.$prefix.'.'.$extension;
            $this->storeDownloadedFile($client, $file['id'], $targetPath);
            $invoice->update([$column => $targetPath]);
        }
    }

    private function restoreAssetFile(
        GoogleDriveClient $client,
        string $folderId,
        string $filename,
        string $targetPath,
        string $column,
        CompanyProfile $profile
    ): void {
        $file = $client->findFileByName($filename, $folderId);

        if ($file === null) {
            return;
        }

        $this->storeDownloadedFile($client, $file['id'], $targetPath);
        $profile->update([$column => $targetPath]);
    }

    private function storeDownloadedFile(GoogleDriveClient $client, string $fileId, string $targetPath): void
    {
        Storage::disk('public')->put($targetPath, $client->downloadFile($fileId));
    }

    private function extension(string $filename): string
    {
        return pathinfo($filename, PATHINFO_EXTENSION) ?: 'png';
    }
}
