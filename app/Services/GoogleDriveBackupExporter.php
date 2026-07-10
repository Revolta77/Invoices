<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\User;
use App\Support\GoogleDrive\GoogleDriveClient;
use App\Support\InvoicePreviewBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class GoogleDriveBackupExporter
{
    public function __construct(
        private InvoicePdfService $pdfService,
    ) {}

    public function export(User $user): void
    {
        if (! $user->canSyncToGoogleDrive()) {
            throw new \RuntimeException('Google Drive záloha nie je dostupná. Prepojte Google účet.');
        }

        $client = new GoogleDriveClient($user);
        $rootFolderId = $client->findOrCreateRootFolder();
        $payload = $this->buildPayload($user);
        $backupFilename = (string) config('google-drive.backup_filename', 'backup.json');

        $existingBackup = $client->findFileByName($backupFilename, $rootFolderId);
        $client->uploadOrUpdateFile(
            $rootFolderId,
            $backupFilename,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'application/json',
            $existingBackup['id'] ?? null
        );

        foreach ($user->companyProfiles()->get() as $profile) {
            $this->uploadProfileAssets($client, $rootFolderId, $profile);
        }

        $invoices = Invoice::query()
            ->where('user_id', $user->id)
            ->with(['items', 'emailLogs'])
            ->get();

        foreach ($invoices as $invoice) {
            $this->uploadInvoiceAssets($client, $rootFolderId, $invoice);
            $this->uploadInvoicePdf($client, $rootFolderId, $invoice);
        }

        $profiles = $user->companyProfiles()->get();

        $this->pruneRemovedRemoteFiles($client, $rootFolderId, $invoices, $profiles);
    }

    public function removeInvoiceFromDrive(User $user, Invoice $invoice): void
    {
        if (! $user->canSyncToGoogleDrive()) {
            return;
        }

        $client = new GoogleDriveClient($user);
        $rootFolderId = $client->findOrCreateRootFolder();
        $year = $invoice->issue_date?->format('Y') ?? now()->format('Y');
        $pdfFilename = InvoicePreviewBuilder::filename($invoice);

        $yearFolderId = $client->findFolder($year, $rootFolderId);

        if ($yearFolderId !== null) {
            $client->deleteFileByName($pdfFilename, $yearFolderId);
        }

        $assetsFolderId = $client->findFolder('assets', $rootFolderId);

        if ($assetsFolderId === null) {
            return;
        }

        $invoicesFolderId = $client->findFolder('invoices', $assetsFolderId);

        if ($invoicesFolderId === null) {
            return;
        }

        $invoiceFolderId = $client->findFolder((string) $invoice->id, $invoicesFolderId);

        if ($invoiceFolderId !== null) {
            $client->deleteFileTree($invoiceFolderId);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(User $user): array
    {
        $profiles = $user->companyProfiles()->get()->map(function (CompanyProfile $profile) {
            return [
                'backup_id' => $profile->id,
                'name' => $profile->name,
                'street' => $profile->street,
                'postal_code' => $profile->postal_code,
                'city' => $profile->city,
                'country' => $profile->country,
                'ico' => $profile->ico,
                'dic' => $profile->dic,
                'taxpayer_type' => $profile->taxpayer_type?->value,
                'ic_dph' => $profile->ic_dph,
                'registry' => $profile->registry,
                'email' => $profile->email,
                'phone' => $profile->phone,
                'web' => $profile->web,
                'logo_path' => $profile->logo_path,
                'stamp_path' => $profile->stamp_path,
                'assets' => [
                    'logo' => $profile->logo_path ? 'assets/profiles/'.$profile->id.'/logo'.'.'.pathinfo($profile->logo_path, PATHINFO_EXTENSION) : null,
                    'stamp' => $profile->stamp_path ? 'assets/profiles/'.$profile->id.'/stamp'.'.'.pathinfo($profile->stamp_path, PATHINFO_EXTENSION) : null,
                ],
            ];
        })->values()->all();

        $invoices = Invoice::query()
            ->where('user_id', $user->id)
            ->with(['items', 'emailLogs'])
            ->orderBy('issue_date')
            ->get()
            ->map(function (Invoice $invoice) {
                $year = $invoice->issue_date?->format('Y') ?? now()->format('Y');
                $pdfFilename = InvoicePreviewBuilder::filename($invoice);

                return [
                    'backup_id' => $invoice->id,
                    'company_profile_backup_id' => $invoice->company_profile_id,
                    'number' => $invoice->number,
                    'partner_name' => $invoice->partner_name,
                    'partner_ico' => $invoice->partner_ico,
                    'partner_street' => $invoice->partner_street,
                    'partner_postal_code' => $invoice->partner_postal_code,
                    'partner_city' => $invoice->partner_city,
                    'partner_country' => $invoice->partner_country,
                    'partner_dic' => $invoice->partner_dic,
                    'partner_ic_dph' => $invoice->partner_ic_dph,
                    'issue_date' => $invoice->issue_date?->toDateString(),
                    'delivery_date' => $invoice->delivery_date?->toDateString(),
                    'due_date' => $invoice->due_date?->toDateString(),
                    'due_days' => $invoice->due_days,
                    'is_identified_person' => $invoice->is_identified_person,
                    'currency' => $invoice->currency,
                    'exchange_rate' => $invoice->exchange_rate,
                    'iban' => $invoice->iban,
                    'bank_account' => $invoice->bank_account,
                    'payment_method' => $invoice->payment_method?->value,
                    'status' => $invoice->status?->value,
                    'paid_at' => $invoice->paid_at?->toIso8601String(),
                    'emailed_at' => $invoice->emailed_at?->toIso8601String(),
                    'paid_amount' => $invoice->paid_amount,
                    'paid_payment_method' => $invoice->paid_payment_method?->value,
                    'total' => $invoice->total,
                    'signature_enabled' => $invoice->signature_enabled,
                    'signature_path' => $invoice->signature_path,
                    'signature_text' => $invoice->signature_text,
                    'logo_enabled' => $invoice->logo_enabled,
                    'logo_path' => $invoice->logo_path,
                    'pdf' => $year.'/'.$pdfFilename,
                    'items' => $invoice->items->map(fn ($item) => [
                        'position' => $item->position,
                        'name' => $item->name,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'unit_price' => $item->unit_price,
                        'total' => $item->total,
                    ])->values()->all(),
                    'email_logs' => $invoice->emailLogs->map(fn ($log) => [
                        'partner_ico' => $log->partner_ico,
                        'partner_name' => $log->partner_name,
                        'to_email' => $log->to_email,
                        'cc_email' => $log->cc_email,
                        'from_email' => $log->from_email,
                        'subject' => $log->subject,
                        'sent_at' => $log->sent_at?->toIso8601String(),
                    ])->values()->all(),
                ];
            })->values()->all();

        return [
            'schema_version' => (int) config('google-drive.schema_version', 1),
            'exported_at' => now()->toIso8601String(),
            'app' => config('app.name'),
            'user_email' => $user->email,
            'company_profiles' => $profiles,
            'invoices' => $invoices,
        ];
    }

    private function uploadProfileAssets(GoogleDriveClient $client, string $rootFolderId, CompanyProfile $profile): void
    {
        $assetsFolderId = $client->findOrCreateFolder('assets', $rootFolderId);
        $profilesFolderId = $client->findOrCreateFolder('profiles', $assetsFolderId);
        $profileFolderId = $client->findOrCreateFolder((string) $profile->id, $profilesFolderId);

        if ($profile->logo_path) {
            $this->uploadStorageFile(
                $client,
                $profileFolderId,
                'logo.'.pathinfo($profile->logo_path, PATHINFO_EXTENSION),
                $profile->logo_path
            );
        }

        if ($profile->stamp_path) {
            $this->uploadStorageFile(
                $client,
                $profileFolderId,
                'stamp.'.pathinfo($profile->stamp_path, PATHINFO_EXTENSION),
                $profile->stamp_path
            );
        }
    }

    private function uploadInvoiceAssets(GoogleDriveClient $client, string $rootFolderId, Invoice $invoice): void
    {
        if (! $invoice->signature_path && ! $invoice->logo_path) {
            return;
        }

        $assetsFolderId = $client->findOrCreateFolder('assets', $rootFolderId);
        $invoicesFolderId = $client->findOrCreateFolder('invoices', $assetsFolderId);
        $invoiceFolderId = $client->findOrCreateFolder((string) $invoice->id, $invoicesFolderId);

        if ($invoice->signature_path) {
            $this->uploadStorageFile(
                $client,
                $invoiceFolderId,
                'signature.'.pathinfo($invoice->signature_path, PATHINFO_EXTENSION),
                $invoice->signature_path
            );
        }

        if ($invoice->logo_path) {
            $this->uploadStorageFile(
                $client,
                $invoiceFolderId,
                'logo.'.pathinfo($invoice->logo_path, PATHINFO_EXTENSION),
                $invoice->logo_path
            );
        }
    }

    private function uploadInvoicePdf(GoogleDriveClient $client, string $rootFolderId, Invoice $invoice): void
    {
        $year = $invoice->issue_date?->format('Y') ?? now()->format('Y');
        $yearFolderId = $client->findOrCreateFolder($year, $rootFolderId);
        $filename = InvoicePreviewBuilder::filename($invoice);
        $pdfBinary = $this->pdfService->output($invoice);
        $existing = $client->findFileByName($filename, $yearFolderId);

        $client->uploadOrUpdateFile(
            $yearFolderId,
            $filename,
            $pdfBinary,
            'application/pdf',
            $existing['id'] ?? null
        );
    }

    /**
     * @param  Collection<int, Invoice>  $invoices
     * @param  Collection<int, CompanyProfile>  $profiles
     */
    private function pruneRemovedRemoteFiles(
        GoogleDriveClient $client,
        string $rootFolderId,
        Collection $invoices,
        Collection $profiles
    ): void {
        $expectedPdfsByYear = [];

        foreach ($invoices as $invoice) {
            $year = $invoice->issue_date?->format('Y') ?? now()->format('Y');
            $expectedPdfsByYear[$year][] = InvoicePreviewBuilder::filename($invoice);
        }

        foreach ($client->listChildren($rootFolderId) as $child) {
            if (($child['mimeType'] ?? '') !== 'application/vnd.google-apps.folder') {
                continue;
            }

            if (! preg_match('/^\d{4}$/', (string) $child['name'])) {
                continue;
            }

            $year = (string) $child['name'];
            $expected = $expectedPdfsByYear[$year] ?? [];

            foreach ($client->listChildren($child['id']) as $file) {
                if (($file['mimeType'] ?? '') === 'application/vnd.google-apps.folder') {
                    continue;
                }

                if (! in_array($file['name'], $expected, true)) {
                    $client->deleteFile($file['id']);
                }
            }
        }

        $assetsFolderId = $client->findFolder('assets', $rootFolderId);

        if ($assetsFolderId === null) {
            return;
        }

        $this->pruneAssetFolders(
            $client,
            $assetsFolderId,
            'invoices',
            $invoices->pluck('id')->map(fn (int $id) => (string) $id)->all()
        );

        $this->pruneAssetFolders(
            $client,
            $assetsFolderId,
            'profiles',
            $profiles->pluck('id')->map(fn (int $id) => (string) $id)->all()
        );
    }

    /**
     * @param  array<int, string>  $expectedFolderNames
     */
    private function pruneAssetFolders(
        GoogleDriveClient $client,
        string $assetsFolderId,
        string $type,
        array $expectedFolderNames
    ): void {
        $typeFolderId = $client->findFolder($type, $assetsFolderId);

        if ($typeFolderId === null) {
            return;
        }

        foreach ($client->listChildren($typeFolderId) as $folder) {
            if (($folder['mimeType'] ?? '') !== 'application/vnd.google-apps.folder') {
                continue;
            }

            if (! in_array($folder['name'], $expectedFolderNames, true)) {
                $client->deleteFileTree($folder['id']);
            }
        }
    }

    private function uploadStorageFile(
        GoogleDriveClient $client,
        string $folderId,
        string $filename,
        string $storagePath
    ): void {
        if (! Storage::disk('public')->exists($storagePath)) {
            return;
        }

        $contents = Storage::disk('public')->get($storagePath);
        $mimeType = Storage::disk('public')->mimeType($storagePath) ?: 'application/octet-stream';
        $existing = $client->findFileByName($filename, $folderId);

        $client->uploadOrUpdateFile(
            $folderId,
            $filename,
            $contents,
            $mimeType,
            $existing['id'] ?? null
        );
    }
}
