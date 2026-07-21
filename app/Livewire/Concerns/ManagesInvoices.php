<?php

namespace App\Livewire\Concerns;

use App\InvoiceStatus;
use App\Mail\InvoiceSentMail;
use App\Models\Invoice;
use App\Models\InvoiceEmailLog;
use App\Models\InvoiceItem;
use App\PaymentMethod;
use App\Services\InvoicePdfService;
use App\Services\SubjektApiService;
use App\Support\InvoiceNumberGenerator;
use App\Support\InvoicePreviewBuilder;
use App\Support\ActiveCompanyProfile;
use App\Services\GoogleDriveBackupExporter;
use App\Support\GoogleDriveBackupDispatcher;
use App\Support\PayBySquare\Generator as PayBySquareGenerator;
use App\Support\SwiftFromIban;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;

trait ManagesInvoices
{
    #[Url(as: 'invoice', history: true, keep: true)]
    public ?int $selectedInvoiceId = null;

    public bool $selectedInvoiceLocked = false;

    public bool $isCreatingInvoice = false;

    public string $invoiceSearch = '';

    public string $filterPeriod = 'current_year';

    public ?string $filterDateFrom = null;

    public ?string $filterDateTo = null;

    public ?string $filterPartnerIco = null;

    public string $filterPartnerSearch = '';

    public string $sortField = 'issue_date';

    public string $sortDirection = 'desc';

    public bool $showFilterModal = false;

    public bool $showSortModal = false;

    public string $sortFieldDraft = 'number';

    public string $sortDirectionDraft = 'desc';

    /** @var array<string, mixed> */
    public array $invoiceForm = [];

    /** @var array<int, array<string, mixed>> */
    public array $invoiceItems = [];

    /** @var array<int, array<string, string>> */
    public array $partnerSearchResults = [];

    public bool $showPartnerSearchResults = false;

    public $invoiceStamp = null;

    public ?string $existingInvoiceStampUrl = null;

    public bool $removeExistingInvoiceStamp = false;

    public $invoiceLogo = null;

    public ?string $existingInvoiceLogoUrl = null;

    public bool $removeExistingInvoiceLogo = false;

    public bool $showInvoicePreviewModal = false;

    public bool $showInvoiceEmailModal = false;

    /** @var array<string, mixed> */
    public array $invoiceEmailForm = [];

    public $emailExtraAttachment = null;

    public bool $showPaymentModal = false;

    public bool $showDeleteInvoiceModal = false;

    public bool $showToggleLockModal = false;

    public ?int $deleteInvoiceId = null;

    public ?int $toggleLockInvoiceId = null;

    public bool $confirmDeleteInvoice = false;

    public bool $toggleLockTargetState = true;

    public ?int $paymentInvoiceId = null;

    /** @var array<string, mixed> */
    public array $paymentForm = [];

    public bool $invoiceHasPayment = false;

    /** @var array<string, mixed> */
    public array $invoicePaymentForm = [];

    public function initInvoiceDashboard(): void
    {
        $this->applyFilterPeriodDates();

        if ($this->selectedInvoiceId) {
            $this->loadInvoice($this->selectedInvoiceId);
        }
    }

    public function startCreateInvoice(): void
    {
        $profileId = ActiveCompanyProfile::id();

        if (! $profileId) {
            return;
        }

        $this->isCreatingInvoice = true;
        $this->selectedInvoiceId = null;
        $this->selectedInvoiceLocked = false;
        $today = now()->toDateString();

        $this->invoiceForm = [
            'number' => InvoiceNumberGenerator::suggest($profileId),
            'partner_name' => '',
            'partner_ico' => '',
            'partner_street' => '',
            'partner_postal_code' => '',
            'partner_city' => '',
            'partner_country' => 'SK',
            'partner_dic' => '',
            'partner_ic_dph' => '',
            'issue_date' => $today,
            'delivery_date' => $today,
            'due_date' => now()->addDays(14)->toDateString(),
            'due_days' => 14,
            'is_identified_person' => false,
            'currency' => 'EUR',
            'exchange_rate' => '',
            'iban' => '',
            'bank_account' => '',
            'payment_method' => PaymentMethod::BankTransfer->value,
            'signature_enabled' => true,
            'logo_enabled' => true,
        ];

        $this->resetInvoiceStampState();
        $this->resetInvoiceLogoState();
        $this->existingInvoiceStampUrl = ActiveCompanyProfile::get()?->stampUrl();
        $this->existingInvoiceLogoUrl = ActiveCompanyProfile::get()?->logoUrl();

        $this->invoiceItems = [
            ['position' => 1, 'name' => '', 'quantity' => '1', 'unit' => '', 'unit_price' => '', 'total' => '0'],
        ];

        $this->invoiceHasPayment = false;
        $this->invoicePaymentForm = [];

        $this->partnerSearchResults = [];
        $this->showPartnerSearchResults = false;
    }

    public function selectInvoice(int $invoiceId): void
    {
        $this->isCreatingInvoice = false;
        $this->selectedInvoiceId = $invoiceId;
        $this->loadInvoice($invoiceId);
    }

    public function loadInvoice(int $invoiceId): void
    {
        $invoice = $this->invoiceQuery()->with('items')->findOrFail($invoiceId);
        $this->selectedInvoiceLocked = $invoice->isLocked();

        $this->invoiceForm = [
            'number' => $invoice->number,
            'partner_name' => $invoice->partner_name,
            'partner_ico' => $invoice->partner_ico ?? '',
            'partner_street' => $invoice->partner_street ?? '',
            'partner_postal_code' => $invoice->partner_postal_code ?? '',
            'partner_city' => $invoice->partner_city ?? '',
            'partner_country' => $invoice->partner_country ?? 'SK',
            'partner_dic' => $invoice->partner_dic ?? '',
            'partner_ic_dph' => $invoice->partner_ic_dph ?? '',
            'issue_date' => $invoice->issue_date->toDateString(),
            'delivery_date' => $invoice->delivery_date?->toDateString() ?? '',
            'due_date' => $invoice->due_date?->toDateString() ?? '',
            'due_days' => $invoice->due_days ?? 14,
            'is_identified_person' => $invoice->is_identified_person,
            'currency' => $invoice->currency,
            'exchange_rate' => $invoice->exchange_rate !== null ? (string) $invoice->exchange_rate : '',
            'iban' => $invoice->iban ?? '',
            'bank_account' => $invoice->bank_account ?? '',
            'payment_method' => $invoice->payment_method->value,
            'signature_enabled' => $invoice->signature_enabled,
            'logo_enabled' => $invoice->logo_enabled,
        ];

        $this->resetInvoiceStampState();
        $this->resetInvoiceLogoState();
        $this->existingInvoiceStampUrl = $this->resolveInvoiceStampPreviewUrl($invoice);
        $this->existingInvoiceLogoUrl = $this->resolveInvoiceLogoPreviewUrl($invoice);

        $this->invoiceItems = $invoice->items->map(fn (InvoiceItem $item) => [
            'id' => $item->id,
            'position' => $item->position,
            'name' => $item->name,
            'quantity' => $this->formatDecimalForInput($item->quantity),
            'unit' => $item->unit,
            'unit_price' => $this->formatDecimalForInput($item->unit_price),
            'total' => (string) $item->total,
        ])->values()->all();

        if ($this->invoiceItems === []) {
            $this->invoiceItems = [
                ['position' => 1, 'name' => '', 'quantity' => '1', 'unit' => '', 'unit_price' => '', 'total' => '0'],
            ];
        }

        $this->populateInvoicePaymentForm($invoice);
    }

    public function closeInvoicePanel(): void
    {
        $this->isCreatingInvoice = false;
        $this->selectedInvoiceId = null;
        $this->selectedInvoiceLocked = false;
        $this->invoiceForm = [];
        $this->invoiceItems = [];
        $this->invoiceHasPayment = false;
        $this->invoicePaymentForm = [];
        $this->resetInvoiceStampState();
        $this->resetInvoiceLogoState();
    }

    public function removeInvoiceLogo(): void
    {
        if (! $this->ensureInvoiceIsEditable()) {
            return;
        }

        $this->invoiceLogo = null;

        if ($this->invoiceHasCustomLogo()) {
            $this->removeExistingInvoiceLogo = true;
        }

        $this->existingInvoiceLogoUrl = ActiveCompanyProfile::get()?->logoUrl();
    }

    public function updatedInvoiceLogo(): void
    {
        $this->removeExistingInvoiceLogo = false;

        if ($this->invoiceLogo !== null) {
            try {
                $this->existingInvoiceLogoUrl = $this->invoiceLogo->temporaryUrl();
            } catch (\Throwable) {
                //
            }
        }
    }

    public function removeInvoiceStamp(): void
    {
        if (! $this->ensureInvoiceIsEditable()) {
            return;
        }

        $this->invoiceStamp = null;

        if ($this->invoiceHasCustomStamp()) {
            $this->removeExistingInvoiceStamp = true;
        }

        $this->existingInvoiceStampUrl = ActiveCompanyProfile::get()?->stampUrl();
    }

    public function updatedInvoiceStamp(): void
    {
        $this->removeExistingInvoiceStamp = false;

        if ($this->invoiceStamp !== null) {
            try {
                $this->existingInvoiceStampUrl = $this->invoiceStamp->temporaryUrl();
            } catch (\Throwable) {
                //
            }
        }
    }

    public function saveInvoice(): void
    {
        $profileId = ActiveCompanyProfile::id();

        if (! $profileId) {
            return;
        }

        if (! $this->ensureInvoiceIsEditable()) {
            return;
        }

        $this->prepareInvoiceItemsForSave();

        $rules = [
            'invoiceForm.number' => ['required', 'string', 'max:32'],
            'invoiceForm.partner_name' => ['required', 'string', 'max:255'],
            'invoiceForm.issue_date' => ['required', 'date'],
            'invoiceForm.delivery_date' => ['nullable', 'date'],
            'invoiceForm.due_date' => ['nullable', 'date'],
            'invoiceForm.payment_method' => ['required', 'in:cash,bank_transfer'],
            'invoiceItems' => ['required', 'array', 'min:1'],
            'invoiceItems.*.name' => ['required', 'string', 'max:255'],
            'invoiceItems.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'invoiceItems.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];

        if ((bool) ($this->invoiceForm['signature_enabled'] ?? false)) {
            $rules['invoiceStamp'] = ['nullable', 'image', 'mimes:png', 'max:2048'];
        }

        if ((bool) ($this->invoiceForm['logo_enabled'] ?? false)) {
            $rules['invoiceLogo'] = ['nullable', 'image', 'mimes:png', 'max:2048'];
        }

        if ($this->invoiceHasPayment) {
            $rules['invoicePaymentForm.paid_at'] = ['required', 'date'];
            $rules['invoicePaymentForm.payment_method'] = ['required', 'in:cash,bank_transfer'];
            $rules['invoicePaymentForm.amount'] = ['required', 'numeric', 'min:0.01'];
        }

        $validator = Validator::make(
            [
                'invoiceForm' => $this->invoiceForm,
                'invoiceItems' => $this->invoiceItems,
                'invoiceStamp' => $this->invoiceStamp,
                'invoiceLogo' => $this->invoiceLogo,
                'invoicePaymentForm' => $this->invoicePaymentForm,
            ],
            $rules,
            [
                'invoiceForm.partner_name.required' => __('app.validation.invoice.partner_name_required'),
                'invoiceForm.number.required' => __('app.validation.invoice.number_required'),
                'invoiceItems.*.name.required' => __('app.validation.invoice.item_name_required'),
                'invoiceStamp.mimes' => __('app.validation.invoice.stamp_mimes'),
                'invoiceLogo.mimes' => __('app.validation.invoice.logo_mimes'),
            ]
        );

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            $this->dispatch('scroll-to-first-error');

            return;
        }

        if (! InvoiceNumberGenerator::isAvailable($profileId, $this->invoiceForm['number'], $this->selectedInvoiceId)) {
            $this->addError('invoiceForm.number', __('app.validation.invoice.number_exists'));
            $this->dispatch('scroll-to-first-error');

            return;
        }

        if ($this->invoiceHasPayment) {
            $paymentAmount = round($this->parseDecimal($this->invoicePaymentForm['amount']), 2);
            $invoiceTotal = round($this->invoiceGrandTotal(), 2);

            if (abs($paymentAmount - $invoiceTotal) > 0.009) {
                $this->addError('invoicePaymentForm.amount', __('app.validation.invoice.payment_amount_mismatch'));
                $this->dispatch('scroll-to-first-error');

                return;
            }
        }

        $this->recalculateAllItemTotals();

        $data = [
            'company_profile_id' => $profileId,
            'user_id' => auth()->id(),
            'number' => $this->invoiceForm['number'],
            'partner_name' => $this->invoiceForm['partner_name'],
            'partner_ico' => $this->invoiceForm['partner_ico'] ?: null,
            'partner_street' => $this->invoiceForm['partner_street'] ?: null,
            'partner_postal_code' => $this->invoiceForm['partner_postal_code'] ?: null,
            'partner_city' => $this->invoiceForm['partner_city'] ?: null,
            'partner_country' => $this->invoiceForm['partner_country'] ?: 'SK',
            'partner_dic' => $this->invoiceForm['partner_dic'] ?: null,
            'partner_ic_dph' => $this->invoiceForm['partner_ic_dph'] ?: null,
            'issue_date' => $this->invoiceForm['issue_date'],
            'delivery_date' => $this->invoiceForm['delivery_date'] ?: null,
            'due_date' => $this->invoiceForm['due_date'] ?: null,
            'due_days' => (int) ($this->invoiceForm['due_days'] ?? 0) ?: null,
            'is_identified_person' => (bool) $this->invoiceForm['is_identified_person'],
            'currency' => $this->invoiceForm['currency'] ?: 'EUR',
            'exchange_rate' => filled($this->invoiceForm['exchange_rate']) ? $this->invoiceForm['exchange_rate'] : null,
            'iban' => $this->invoiceForm['iban'] ?: null,
            'bank_account' => $this->invoiceForm['bank_account'] ?: null,
            'payment_method' => $this->invoiceForm['payment_method'],
            'signature_enabled' => (bool) $this->invoiceForm['signature_enabled'],
            'logo_enabled' => (bool) $this->invoiceForm['logo_enabled'],
            'total' => $this->invoiceGrandTotal(),
        ];

        DB::transaction(function () use ($data) {
            if ($this->selectedInvoiceId) {
                $invoice = $this->invoiceQuery()->findOrFail($this->selectedInvoiceId);
                $invoice->update($data);
            } else {
                $invoice = Invoice::query()->create($data);
                $this->selectedInvoiceId = $invoice->id;
                $this->isCreatingInvoice = false;
            }

            $invoice->items()->delete();

            foreach ($this->invoiceItems as $index => $row) {
                $invoice->items()->create([
                    'position' => $index + 1,
                    'name' => $row['name'],
                    'quantity' => $row['quantity'],
                    'unit' => $row['unit'] ?: 'ks',
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                ]);
            }

            $invoice->recalculateTotal();
            $this->applyInvoicePaymentForm($invoice);
            $invoice->save();

            if ((bool) $this->invoiceForm['signature_enabled']) {
                if ($this->removeExistingInvoiceStamp && $this->invoiceStamp === null) {
                    $this->deleteInvoiceStampFile($invoice);
                }

                $this->storeInvoiceStamp($invoice);
            } else {
                $this->deleteInvoiceStampFile($invoice);
                $this->invoiceStamp = null;
                $this->removeExistingInvoiceStamp = false;
            }

            if ((bool) $this->invoiceForm['logo_enabled']) {
                if ($this->removeExistingInvoiceLogo && $this->invoiceLogo === null) {
                    $this->deleteInvoiceLogoFile($invoice);
                }

                $this->storeInvoiceLogo($invoice);
            } else {
                $this->deleteInvoiceLogoFile($invoice);
                $this->invoiceLogo = null;
                $this->removeExistingInvoiceLogo = false;
            }
        });

        $this->removeExistingInvoiceStamp = false;
        $this->invoiceStamp = null;
        $this->removeExistingInvoiceLogo = false;
        $this->invoiceLogo = null;

        if ($this->selectedInvoiceId) {
            $invoice = $this->invoiceQuery()->find($this->selectedInvoiceId);
            $this->existingInvoiceStampUrl = $invoice
                ? $this->resolveInvoiceStampPreviewUrl($invoice)
                : ActiveCompanyProfile::get()?->stampUrl();
            $this->existingInvoiceLogoUrl = $invoice
                ? $this->resolveInvoiceLogoPreviewUrl($invoice)
                : ActiveCompanyProfile::get()?->logoUrl();

            if ($invoice) {
                $this->populateInvoicePaymentForm($invoice);
            }
        }

        session()->flash('status', __('app.messages.invoice_saved'));

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function previewInvoice(): void
    {
        $this->recalculateAllItemTotals();
        $this->showInvoicePreviewModal = true;
    }

    public function closeInvoicePreview(): void
    {
        $this->showInvoicePreviewModal = false;
    }

    public function printInvoice(): void
    {
        if (! $this->ensureInvoiceSavedForExport()) {
            return;
        }

        $this->dispatch('invoice-print', url: route('invoices.pdf', [
            'invoice' => $this->selectedInvoiceId,
            'inline' => 1,
        ]));
    }

    public function sendInvoice(): void
    {
        $this->recalculateAllItemTotals();
        $profile = ActiveCompanyProfile::get();

        $this->invoiceEmailForm = [
            'to' => $this->resolvePartnerEmailForInvoice() ?? '',
            'cc' => '',
            'from' => $profile?->email ?: (string) config('mail.from.address'),
            'locale' => $this->defaultInvoiceEmailLocale(),
            'subject' => '',
            'body' => '',
        ];

        $this->refreshInvoiceEmailDefaults($this->invoiceEmailForm['locale']);
        $this->emailExtraAttachment = null;
        $this->resetErrorBag();
        $this->showInvoiceEmailModal = true;
    }

    public function updatedInvoiceEmailFormLocale(?string $value): void
    {
        if (! is_string($value) || ! array_key_exists($value, config('locales.available', []))) {
            return;
        }

        $this->refreshInvoiceEmailDefaults($value);
    }

    public function closeInvoiceEmailModal(): void
    {
        $this->showInvoiceEmailModal = false;
        $this->emailExtraAttachment = null;
    }

    public function sendInvoiceEmail(): void
    {
        $this->recalculateAllItemTotals();

        if (! $this->ensureInvoiceSavedForExport()) {
            $this->addError('invoiceEmail', __('app.validation.invoice.email_check_before_send'));

            return;
        }

        $rules = [
            'invoiceEmailForm.to' => ['required', 'email', 'max:255'],
            'invoiceEmailForm.cc' => ['nullable', 'string', 'max:500'],
            'invoiceEmailForm.from' => ['required', 'email', 'max:255'],
            'invoiceEmailForm.locale' => ['required', Rule::in(array_keys(config('locales.available', [])))],
            'invoiceEmailForm.subject' => ['required', 'string', 'max:255'],
            'invoiceEmailForm.body' => ['required', 'string', 'max:5000'],
            'emailExtraAttachment' => ['nullable', 'file', 'max:10240'],
        ];

        $validator = Validator::make(
            [
                'invoiceEmailForm' => $this->invoiceEmailForm,
                'emailExtraAttachment' => $this->emailExtraAttachment,
            ],
            $rules,
            [
                'invoiceEmailForm.to.required' => __('app.validation.invoice.email_to_required'),
                'invoiceEmailForm.to.email' => __('app.validation.invoice.email_to_invalid'),
                'invoiceEmailForm.from.required' => __('app.validation.invoice.email_from_required'),
                'invoiceEmailForm.subject.required' => __('app.validation.invoice.email_subject_required'),
                'invoiceEmailForm.body.required' => __('app.validation.invoice.email_body_required'),
            ],
        );

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());

            return;
        }

        $profile = ActiveCompanyProfile::get();
        $profileId = ActiveCompanyProfile::id();
        $userId = auth()->id();

        if (! $profile || ! $profileId || ! $userId) {
            return;
        }

        $invoice = $this->invoiceQuery()->with('items')->findOrFail($this->selectedInvoiceId);

        try {
            $pdfBinary = app(InvoicePdfService::class)->output($invoice);
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('invoiceEmail', __('app.validation.invoice.pdf_generation_failed'));

            return;
        }

        $preview = InvoicePreviewBuilder::fromInvoice($invoice);
        $emailLocale = $this->resolveInvoiceEmailLocale();
        $paymentDetails = $this->buildInvoiceEmailPaymentDetails($preview, $emailLocale);
        $paymentDetails['subject'] = $this->invoiceEmailForm['subject'];
        $paymentDetails['from_email'] = $this->invoiceEmailForm['from'];
        $paymentDetails['from_name'] = $profile->name;

        $extraPath = null;
        $extraName = null;

        if ($this->emailExtraAttachment !== null) {
            $extraPath = $this->emailExtraAttachment->getRealPath();
            $extraName = $this->emailExtraAttachment->getClientOriginalName();
        }

        $openToken = (string) Str::uuid();
        $trackingUrl = route('email.open', ['token' => $openToken]);

        try {
            $mail = new InvoiceSentMail(
                messageBody: trim($this->invoiceEmailForm['body']),
                paymentDetails: $paymentDetails,
                pdfFilename: InvoicePreviewBuilder::filename($invoice),
                pdfBinary: $pdfBinary,
                extraAttachmentPath: $extraPath,
                extraAttachmentName: $extraName,
                trackingUrl: $trackingUrl,
            );

            $message = Mail::to($this->invoiceEmailForm['to']);

            if (filled($this->invoiceEmailForm['cc'] ?? null)) {
                $ccAddresses = collect(preg_split('/[;,]+/', $this->invoiceEmailForm['cc']))
                    ->map(fn ($email) => trim($email))
                    ->filter(fn ($email) => filled($email))
                    ->values()
                    ->all();

                if ($ccAddresses !== []) {
                    $message->cc($ccAddresses);
                }
            }

            $message->locale($emailLocale)->send($mail);
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('invoiceEmail', __('app.validation.invoice.email_send_failed'));

            return;
        }

        $sentAt = now();
        $invoice = $this->persistInvoiceEmailMetadata($sentAt);

        InvoiceEmailLog::query()->create([
            'invoice_id' => $invoice?->id,
            'company_profile_id' => $profileId,
            'user_id' => $userId,
            'partner_ico' => $this->invoiceForm['partner_ico'] ?? null,
            'partner_name' => $this->invoiceForm['partner_name'] ?? '',
            'to_email' => $this->invoiceEmailForm['to'],
            'cc_email' => $this->invoiceEmailForm['cc'] ?? null,
            'from_email' => $this->invoiceEmailForm['from'],
            'subject' => $this->invoiceEmailForm['subject'],
            'sent_at' => $sentAt,
            'open_token' => $openToken,
        ]);

        $this->showInvoiceEmailModal = false;
        $this->emailExtraAttachment = null;
        session()->flash('status', __('app.messages.invoice_sent'));

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function downloadInvoice(): void
    {
        if (! $this->ensureInvoiceSavedForExport()) {
            return;
        }

        $this->dispatch('invoice-download', url: route('invoices.pdf', [
            'invoice' => $this->selectedInvoiceId,
        ]));
    }

    public function duplicateInvoice(): void
    {
        if (! $this->selectedInvoiceId) {
            return;
        }

        $this->loadInvoice($this->selectedInvoiceId);
        $this->selectedInvoiceId = null;
        $this->selectedInvoiceLocked = false;
        $this->isCreatingInvoice = true;
        $profileId = ActiveCompanyProfile::id();
        $this->invoiceForm['number'] = InvoiceNumberGenerator::suggest($profileId);
        $this->invoiceForm['issue_date'] = now()->toDateString();
        $this->updatedInvoiceFormIssueDate();
        $this->resetInvoiceStampState();
        $this->resetInvoiceLogoState();
        $this->existingInvoiceStampUrl = ActiveCompanyProfile::get()?->stampUrl();
        $this->existingInvoiceLogoUrl = ActiveCompanyProfile::get()?->logoUrl();
        $this->invoiceHasPayment = false;
        $this->invoicePaymentForm = [];
    }

    public function addInvoiceItem(): void
    {
        if (! $this->ensureInvoiceIsEditable()) {
            return;
        }

        $this->invoiceItems[] = [
            'position' => count($this->invoiceItems) + 1,
            'name' => '',
            'quantity' => '1',
            'unit' => '',
            'unit_price' => '',
            'total' => '0',
        ];
    }

    public function removeInvoiceItem(int $index): void
    {
        if (! $this->ensureInvoiceIsEditable()) {
            return;
        }

        if (count($this->invoiceItems) <= 1) {
            return;
        }

        unset($this->invoiceItems[$index]);
        $this->invoiceItems = array_values($this->invoiceItems);
    }

    public function updatedInvoiceItems($value, $key): void
    {
        if (! str_contains($key, '.')) {
            return;
        }

        [$index, $field] = explode('.', $key, 2);

        if (in_array($field, ['quantity', 'unit_price'], true)) {
            $this->invoiceItems[$index][$field] = $this->normalizeDecimalInput(
                (string) ($this->invoiceItems[$index][$field] ?? '')
            );
            $this->recalculateItemTotal((int) $index);
        }
    }

    public function updatedInvoiceFormDueDays(): void
    {
        if (empty($this->invoiceForm['issue_date']) || ! filled($this->invoiceForm['due_days'])) {
            return;
        }

        $this->invoiceForm['due_date'] = Carbon::parse($this->invoiceForm['issue_date'])
            ->addDays((int) $this->invoiceForm['due_days'])
            ->toDateString();
    }

    public function updatedInvoiceFormDueDate(): void
    {
        if (empty($this->invoiceForm['issue_date']) || empty($this->invoiceForm['due_date'])) {
            return;
        }

        $issue = Carbon::parse($this->invoiceForm['issue_date']);
        $due = Carbon::parse($this->invoiceForm['due_date']);
        $this->invoiceForm['due_days'] = max(0, $issue->diffInDays($due, false));
    }

    public function updatedInvoiceFormIssueDate(): void
    {
        $this->updatedInvoiceFormDueDays();
    }

    public function focusPartnerField(): void
    {
        if (trim($this->invoiceForm['partner_name'] ?? '') === '') {
            $this->partnerSearchResults = $this->knownPartners();
            $this->showPartnerSearchResults = count($this->partnerSearchResults) > 0;
        }
    }

    public function updatedInvoiceFormPartnerName(): void
    {
        $query = trim($this->invoiceForm['partner_name'] ?? '');

        if (mb_strlen($query) < 2) {
            $this->partnerSearchResults = $this->knownPartners($query);
            $this->showPartnerSearchResults = count($this->partnerSearchResults) > 0;

            return;
        }

        $apiResults = app(SubjektApiService::class)->search($query);
        $this->partnerSearchResults = $apiResults;
        $this->showPartnerSearchResults = count($apiResults) > 0;
    }

    public function selectPartnerFromList(int $index): void
    {
        $partner = $this->partnerSearchResults[$index] ?? null;

        if (! $partner) {
            return;
        }

        if (! empty($partner['ico'])) {
            $this->selectPartner($partner['ico']);

            return;
        }

        $this->invoiceForm['partner_name'] = $partner['name'] ?? '';
        $this->invoiceForm['partner_city'] = $partner['city'] ?? '';
        $this->showPartnerSearchResults = false;
    }

    public function duplicateInvoiceFromList(int $invoiceId): void
    {
        $this->selectInvoice($invoiceId);
        $this->duplicateInvoice();
    }

    public function openDeleteInvoiceModal(int $invoiceId): void
    {
        $this->invoiceQuery()->findOrFail($invoiceId);

        $this->deleteInvoiceId = $invoiceId;
        $this->confirmDeleteInvoice = false;
        $this->resetErrorBag('confirmDeleteInvoice');
        $this->showDeleteInvoiceModal = true;
    }

    public function closeDeleteInvoiceModal(): void
    {
        $this->showDeleteInvoiceModal = false;
        $this->deleteInvoiceId = null;
        $this->confirmDeleteInvoice = false;
        $this->resetErrorBag('confirmDeleteInvoice');
    }

    public function deleteInvoice(): void
    {
        if (! $this->deleteInvoiceId) {
            return;
        }

        if (! $this->confirmDeleteInvoice) {
            $this->addError('confirmDeleteInvoice', __('app.validation.invoice.delete_confirm_required'));

            return;
        }

        $invoice = $this->invoiceQuery()->findOrFail($this->deleteInvoiceId);
        $deletedId = $invoice->id;
        $invoiceNumber = $invoice->number;

        if ($invoice->signature_path) {
            Storage::disk('public')->delete($invoice->signature_path);
        }

        if ($invoice->logo_path) {
            Storage::disk('public')->delete($invoice->logo_path);
        }

        Storage::disk('public')->deleteDirectory('invoices/'.$invoice->id);

        if (config('google-drive.backup_enabled') && auth()->user()?->canSyncToGoogleDrive()) {
            try {
                app(GoogleDriveBackupExporter::class)->removeInvoiceFromDrive(auth()->user(), $invoice);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $invoice->delete();

        if ($this->selectedInvoiceId === $deletedId) {
            $this->selectedInvoiceId = null;
            $this->isCreatingInvoice = false;
            $this->invoiceForm = [];
            $this->invoiceItems = [];
            $this->resetInvoiceStampState();
            $this->resetInvoiceLogoState();
            $this->invoiceHasPayment = false;
            $this->invoicePaymentForm = [];
        }

        $this->closeDeleteInvoiceModal();

        session()->flash('status', __('app.messages.invoice_deleted', ['number' => $invoiceNumber]));

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function openPaymentModal(int $invoiceId): void
    {
        $invoice = $this->invoiceQuery()->findOrFail($invoiceId);

        if ($invoice->status === InvoiceStatus::Paid || $invoice->isLocked()) {
            return;
        }

        $this->paymentInvoiceId = $invoiceId;
        $this->paymentForm = [
            'paid_at' => now()->toDateString(),
            'payment_method' => $invoice->payment_method->value,
            'amount' => $this->formatDecimalForInput($invoice->total),
        ];
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->paymentInvoiceId = null;
        $this->paymentForm = [];
    }

    public function updatedPaymentForm($value, $key): void
    {
        if ($key === 'amount') {
            $this->paymentForm['amount'] = $this->normalizeDecimalInput(
                (string) ($this->paymentForm['amount'] ?? '')
            );
        }
    }

    public function updatedInvoicePaymentForm($value, $key): void
    {
        if ($key === 'amount') {
            $this->invoicePaymentForm['amount'] = $this->normalizeDecimalInput(
                (string) ($this->invoicePaymentForm['amount'] ?? '')
            );
        }
    }

    public function addPayment(): void
    {
        if (! $this->paymentInvoiceId) {
            return;
        }

        $invoice = $this->invoiceQuery()->findOrFail($this->paymentInvoiceId);

        if ($invoice->isLocked()) {
            $this->closePaymentModal();
            $this->flashLockedInvoiceMessage();

            return;
        }

        if ($invoice->status === InvoiceStatus::Paid) {
            $this->closePaymentModal();

            return;
        }

        Validator::validate(
            ['paymentForm' => $this->paymentForm],
            [
                'paymentForm.paid_at' => ['required', 'date'],
                'paymentForm.payment_method' => ['required', 'in:'.implode(',', array_column(PaymentMethod::cases(), 'value'))],
                'paymentForm.amount' => ['required', 'numeric', 'min:0.01'],
            ],
            [
                'paymentForm.paid_at.required' => __('app.validation.invoice.payment_date_required'),
                'paymentForm.payment_method.required' => __('app.validation.invoice.payment_method_required'),
                'paymentForm.amount.required' => __('app.validation.invoice.payment_amount_required'),
            ]
        );

        $amount = round($this->parseDecimal($this->paymentForm['amount']), 2);
        $invoiceTotal = round((float) $invoice->total, 2);

        if (abs($amount - $invoiceTotal) > 0.009) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'paymentForm.amount' => __('app.validation.invoice.payment_amount_mismatch'),
            ]);
        }

        $invoice->paid_at = Carbon::parse($this->paymentForm['paid_at'])->startOfDay();
        $invoice->paid_payment_method = PaymentMethod::from($this->paymentForm['payment_method']);
        $invoice->paid_amount = $amount;
        $invoice->refreshStatus();
        $invoice->save();

        $this->closePaymentModal();

        if ($this->selectedInvoiceId === $invoice->id) {
            $this->loadInvoice($invoice->id);
        }

        session()->flash('status', __('app.messages.payment_recorded'));

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function selectPartner(string $ico): void
    {
        $partner = collect($this->partnerSearchResults)->firstWhere('ico', $ico);

        if ($partner && isset($partner['ico']) && strlen(preg_replace('/\D/', '', $partner['ico']) ?? '') === 8) {
            $entity = app(SubjektApiService::class)->entity($partner['ico']);

            if ($entity) {
                $mapped = app(SubjektApiService::class)->mapToFormFields($entity);
                $this->invoiceForm['partner_name'] = $mapped['name'];
                $this->invoiceForm['partner_ico'] = $mapped['ico'];
                $this->invoiceForm['partner_street'] = $mapped['street'];
                $this->invoiceForm['partner_postal_code'] = $mapped['postal_code'];
                $this->invoiceForm['partner_city'] = $mapped['city'];
                $this->invoiceForm['partner_country'] = $mapped['country'];
                $this->invoiceForm['partner_dic'] = $mapped['dic'];
                $this->invoiceForm['partner_ic_dph'] = $mapped['ic_dph'];
                $this->showPartnerSearchResults = false;

                return;
            }
        }

        if ($partner) {
            $this->invoiceForm['partner_name'] = $partner['name'] ?? '';
            $this->invoiceForm['partner_ico'] = $partner['ico'] ?? '';
            $this->invoiceForm['partner_city'] = $partner['city'] ?? '';
        }

        $this->showPartnerSearchResults = false;
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function openToggleLockModal(?int $invoiceId = null): void
    {
        $invoiceId ??= $this->selectedInvoiceId;

        if (! $invoiceId) {
            return;
        }

        $invoice = $this->invoiceQuery()->findOrFail($invoiceId);
        $this->toggleLockInvoiceId = $invoice->id;
        $this->toggleLockTargetState = ! $invoice->isLocked();
        $this->showToggleLockModal = true;
    }

    public function closeToggleLockModal(): void
    {
        $this->showToggleLockModal = false;
        $this->toggleLockInvoiceId = null;
        $this->toggleLockTargetState = true;
    }

    public function toggleInvoiceLock(): void
    {
        if (! $this->toggleLockInvoiceId) {
            return;
        }

        $invoice = $this->invoiceQuery()->findOrFail($this->toggleLockInvoiceId);

        if ($this->toggleLockTargetState) {
            $invoice->lock();
            session()->flash('status', __('app.messages.invoice_locked', ['number' => $invoice->number]));
        } else {
            $invoice->unlock();
            session()->flash('status', __('app.messages.invoice_unlocked', ['number' => $invoice->number]));
        }

        if ($this->selectedInvoiceId === $invoice->id) {
            $this->loadInvoice($invoice->id);
        }

        $this->closeToggleLockModal();

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function applyFilters(): void
    {
        $this->applyFilterPeriodDates();
        $this->showFilterModal = false;
    }

    public function clearFilters(): void
    {
        $this->filterPeriod = 'all';
        $this->filterDateFrom = null;
        $this->filterDateTo = null;
        $this->filterPartnerIco = null;
        $this->filterPartnerSearch = '';
        $this->applyFilterPeriodDates();
    }

    public function updatedFilterPeriod(): void
    {
        $this->applyFilterPeriodDates();
    }

    public function openSortModal(): void
    {
        $this->sortFieldDraft = $this->sortField === 'issue_date' ? 'number' : $this->sortField;
        $this->sortDirectionDraft = $this->sortDirection;
        $this->showSortModal = true;
    }

    public function applySort(): void
    {
        $this->sortField = $this->sortFieldDraft;
        $this->sortDirection = $this->sortDirectionDraft;
        $this->showSortModal = false;
    }

    public function closeSortModal(): void
    {
        $this->showSortModal = false;
    }

    public function invoiceGrandTotal(): float
    {
        return round(collect($this->invoiceItems)->sum(fn ($row) => (float) ($row['total'] ?? 0)), 2);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInvoicePreviewDataProperty(): array
    {
        $profile = ActiveCompanyProfile::get();
        $this->recalculateAllItemTotals();

        $stampUrl = null;
        if ($this->invoiceForm['signature_enabled'] ?? false) {
            if ($this->invoiceStamp !== null) {
                try {
                    $stampUrl = $this->invoiceStamp->temporaryUrl();
                } catch (\Throwable) {
                    $stampUrl = $this->existingInvoiceStampUrl;
                }
            } else {
                $stampUrl = $this->existingInvoiceStampUrl;
            }
        }

        $logoUrl = null;
        if ($this->invoiceForm['logo_enabled'] ?? false) {
            if ($this->invoiceLogo !== null) {
                try {
                    $logoUrl = $this->invoiceLogo->temporaryUrl();
                } catch (\Throwable) {
                    $logoUrl = $this->existingInvoiceLogoUrl;
                }
            } else {
                $logoUrl = $this->existingInvoiceLogoUrl;
            }
        }

        $paymentMethod = PaymentMethod::tryFrom($this->invoiceForm['payment_method'] ?? '')
            ?? PaymentMethod::BankTransfer;

        $items = collect($this->invoiceItems)
            ->filter(fn ($row) => filled($row['name'] ?? null))
            ->values()
            ->map(fn ($row, $index) => [
                'position' => $index + 1,
                'name' => $row['name'],
                'quantity' => (float) ($row['quantity'] ?? 0),
                'unit' => $row['unit'] ?? 'ks',
                'unit_price' => (float) ($row['unit_price'] ?? 0),
                'total' => (float) ($row['total'] ?? 0),
            ])
            ->all();

        $total = $this->invoiceGrandTotal();
        $iban = filled($this->invoiceForm['iban'] ?? null) ? trim($this->invoiceForm['iban']) : null;
        $isBankTransfer = $paymentMethod === PaymentMethod::BankTransfer;
        $swift = $isBankTransfer ? SwiftFromIban::guess($iban) : null;

        $payBySquarePayload = null;
        $payBySquareQrUrl = null;

        if ($isBankTransfer && filled($iban) && $total > 0) {
            $payBySquarePayload = PayBySquareGenerator::encode([
                'amount' => $total,
                'currency' => $this->invoiceForm['currency'] ?? 'EUR',
                'variable_symbol' => $this->invoiceForm['number'] ?? null,
                'iban' => $iban,
                'swift' => $swift,
                'beneficiary_name' => $profile?->name,
                'note' => __('app.document.pay_note', ['number' => $this->invoiceForm['number'] ?? '']),
            ]);
            $payBySquareQrUrl = PayBySquareGenerator::qrImageUrl($payBySquarePayload);
        }

        return [
            'number' => $this->invoiceForm['number'] ?? '',
            'issue_date' => $this->formatPreviewDate($this->invoiceForm['issue_date'] ?? null),
            'delivery_date' => $this->formatPreviewDate($this->invoiceForm['delivery_date'] ?? null),
            'due_date' => $this->formatPreviewDate($this->invoiceForm['due_date'] ?? null),
            'currency' => $this->invoiceForm['currency'] ?? 'EUR',
            'iban' => $iban,
            'swift' => $swift,
            'payment_method' => $paymentMethod->label(),
            'payment_method_value' => $paymentMethod->value,
            'is_bank_transfer' => $isBankTransfer,
            'is_identified_person' => (bool) ($this->invoiceForm['is_identified_person'] ?? false),
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
                'name' => $this->invoiceForm['partner_name'] ?? '',
                'street' => $this->invoiceForm['partner_street'] ?? '',
                'postal_code' => $this->invoiceForm['partner_postal_code'] ?? '',
                'city' => $this->invoiceForm['partner_city'] ?? '',
                'country' => $this->invoiceForm['partner_country'] ?? 'SK',
                'ico' => $this->invoiceForm['partner_ico'] ?? '',
                'dic' => $this->invoiceForm['partner_dic'] ?? '',
                'ic_dph' => $this->invoiceForm['partner_ic_dph'] ?? '',
            ],
            'items' => $items,
            'total' => $total,
            'stamp_url' => $stampUrl,
            'pay_by_square_payload' => $payBySquarePayload,
            'pay_by_square_qr_url' => $payBySquareQrUrl,
            'pay_by_square_params' => [
                'amount' => $total,
                'currency' => $this->invoiceForm['currency'] ?? 'EUR',
                'variableSymbol' => $this->invoiceForm['number'] ?? '',
                'iban' => $iban ?? '',
                'swift' => $swift ?? '',
                'beneficiaryName' => $profile?->name ?? '',
                'note' => __('app.document.pay_note', ['number' => $this->invoiceForm['number'] ?? '']),
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Invoice>
     */
    public function getFilteredInvoicesProperty()
    {
        $query = $this->invoiceQuery()->with(['items', 'latestEmailLog']);

        if ($this->invoiceSearch !== '') {
            $term = '%'.$this->invoiceSearch.'%';
            $query->where(function ($q) use ($term) {
                $q->where('partner_name', 'like', $term)
                    ->orWhere('number', 'like', $term)
                    ->orWhere('partner_ico', 'like', $term)
                    ->orWhere('total', 'like', $term);
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('issue_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('issue_date', '<=', $this->filterDateTo);
        }

        if ($this->filterPartnerIco) {
            $query->where('partner_ico', $this->filterPartnerIco);
        }

        $sortColumn = match ($this->sortField) {
            'number' => 'number',
            'partner_name' => 'partner_name',
            'total' => 'total',
            default => 'issue_date',
        };

        $query->orderBy($sortColumn, $this->sortDirection === 'asc' ? 'asc' : 'desc');

        $invoices = $query->get();
        $this->syncInvoiceStatuses($invoices);

        return $invoices;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getFilterPartnerOptionsProperty(): array
    {
        $profileId = ActiveCompanyProfile::id();

        if (! $profileId) {
            return [];
        }

        return Invoice::query()
            ->where('company_profile_id', $profileId)
            ->whereNotNull('partner_ico')
            ->select('partner_name', 'partner_ico')
            ->distinct()
            ->orderBy('partner_name')
            ->get()
            ->map(fn ($row) => [
                'ico' => $row->partner_ico,
                'name' => $row->partner_name,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getFilteredPartnerOptionsProperty(): array
    {
        $options = $this->filterPartnerOptions;

        if ($this->filterPartnerSearch === '') {
            return $options;
        }

        $term = mb_strtolower($this->filterPartnerSearch);

        return array_values(array_filter($options, function (array $partner) use ($term) {
            return str_contains(mb_strtolower($partner['name']), $term)
                || str_contains(mb_strtolower($partner['ico'] ?? ''), $term);
        }));
    }

    /**
     * @return array<int, string>
     */
    public function getKnownIbansProperty(): array
    {
        $profileId = ActiveCompanyProfile::id();

        if (! $profileId) {
            return [];
        }

        return Invoice::query()
            ->where('company_profile_id', $profileId)
            ->whereNotNull('iban')
            ->where('iban', '!=', '')
            ->distinct()
            ->orderBy('iban')
            ->pluck('iban')
            ->all();
    }

    protected function resetInvoiceStampState(): void
    {
        $this->invoiceStamp = null;
        $this->existingInvoiceStampUrl = null;
        $this->removeExistingInvoiceStamp = false;
    }

    protected function resetInvoiceLogoState(): void
    {
        $this->invoiceLogo = null;
        $this->existingInvoiceLogoUrl = null;
        $this->removeExistingInvoiceLogo = false;
    }

    protected function prepareInvoiceItemsForSave(): void
    {
        foreach (array_keys($this->invoiceItems) as $index) {
            $this->invoiceItems[$index]['quantity'] = $this->normalizeDecimalInput(
                (string) ($this->invoiceItems[$index]['quantity'] ?? '')
            );
            $this->invoiceItems[$index]['unit_price'] = $this->normalizeDecimalInput(
                (string) ($this->invoiceItems[$index]['unit_price'] ?? '')
            );

            if ($this->invoiceItems[$index]['quantity'] === '') {
                $this->invoiceItems[$index]['quantity'] = '1';
            }

            if (trim((string) ($this->invoiceItems[$index]['unit'] ?? '')) === '') {
                $this->invoiceItems[$index]['unit'] = 'ks';
            }

            if ($this->invoiceItems[$index]['unit_price'] === '') {
                $this->invoiceItems[$index]['unit_price'] = '0';
            }

            $this->recalculateItemTotal($index);
        }
    }

    protected function normalizeDecimalInput(string $value): string
    {
        $value = trim(str_replace(' ', '', $value));
        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^\d.]/', '', $value) ?? '';

        $firstDot = strpos($value, '.');

        if ($firstDot !== false) {
            $value = substr($value, 0, $firstDot + 1).str_replace('.', '', substr($value, $firstDot + 1));
        }

        return $value;
    }

    protected function parseDecimal(string|float|null $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace(',', '.', (string) $value);
    }

    protected function formatDecimalForInput(string|float|null $value): string
    {
        $string = (string) $value;

        if (! str_contains($string, '.')) {
            return $string;
        }

        return rtrim(rtrim($string, '0'), '.');
    }

    protected function formatPreviewDate(?string $date): ?string
    {
        if (! filled($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('d.m.Y');
        } catch (\Throwable) {
            return $date;
        }
    }

    protected function invoiceHasCustomStamp(): bool
    {
        if ($this->removeExistingInvoiceStamp || ! $this->selectedInvoiceId) {
            return false;
        }

        $invoice = $this->invoiceQuery()->find($this->selectedInvoiceId);

        return filled($invoice?->signature_path);
    }

    protected function resolveInvoiceStampPreviewUrl(?Invoice $invoice = null): ?string
    {
        if ($invoice?->signature_path && ! $this->removeExistingInvoiceStamp) {
            return $invoice->signatureUrl();
        }

        return ActiveCompanyProfile::get()?->stampUrl();
    }

    protected function storeInvoiceStamp(Invoice $invoice): void
    {
        if ($this->invoiceStamp === null) {
            return;
        }

        if ($invoice->signature_path) {
            Storage::disk('public')->delete($invoice->signature_path);
        }

        $path = $this->invoiceStamp->store('invoices/'.$invoice->id, 'public');
        $invoice->update(['signature_path' => $path]);
    }

    protected function deleteInvoiceStampFile(Invoice $invoice): void
    {
        if (! $invoice->signature_path) {
            return;
        }

        Storage::disk('public')->delete($invoice->signature_path);
        $invoice->update(['signature_path' => null]);
    }

    protected function invoiceHasCustomLogo(): bool
    {
        if ($this->removeExistingInvoiceLogo || ! $this->selectedInvoiceId) {
            return false;
        }

        $invoice = $this->invoiceQuery()->find($this->selectedInvoiceId);

        return filled($invoice?->logo_path);
    }

    protected function resolveInvoiceLogoPreviewUrl(?Invoice $invoice = null): ?string
    {
        if ($invoice?->logo_path && ! $this->removeExistingInvoiceLogo) {
            return $invoice->logoUrl();
        }

        return ActiveCompanyProfile::get()?->logoUrl();
    }

    protected function storeInvoiceLogo(Invoice $invoice): void
    {
        if ($this->invoiceLogo === null) {
            return;
        }

        if ($invoice->logo_path) {
            Storage::disk('public')->delete($invoice->logo_path);
        }

        $path = $this->invoiceLogo->store('invoices/'.$invoice->id, 'public');
        $invoice->update(['logo_path' => $path]);
    }

    protected function deleteInvoiceLogoFile(Invoice $invoice): void
    {
        if (! $invoice->logo_path) {
            return;
        }

        Storage::disk('public')->delete($invoice->logo_path);
        $invoice->update(['logo_path' => null]);
    }

    protected function invoiceQuery()
    {
        $profileId = ActiveCompanyProfile::id();

        return Invoice::query()->where('company_profile_id', $profileId);
    }

    protected function ensureInvoiceIsEditable(?Invoice $invoice = null): bool
    {
        if (! $this->selectedInvoiceId && $invoice === null) {
            return true;
        }

        $invoice ??= $this->selectedInvoiceId
            ? $this->invoiceQuery()->find($this->selectedInvoiceId)
            : null;

        if (! $invoice?->isLocked()) {
            return true;
        }

        $this->selectedInvoiceLocked = true;
        $this->flashLockedInvoiceMessage();

        return false;
    }

    protected function flashLockedInvoiceMessage(): void
    {
        session()->flash('status', __('app.validation.invoice.locked'));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, Invoice>  $invoices
     */
    protected function syncInvoiceStatuses($invoices): void
    {
        foreach ($invoices as $invoice) {
            if ($invoice->paid_at !== null) {
                continue;
            }

            $previousStatus = $invoice->status;
            $invoice->refreshStatus();

            if ($invoice->status !== $previousStatus) {
                $invoice->saveQuietly();
            }
        }
    }

    protected function populateInvoicePaymentForm(Invoice $invoice): void
    {
        if ($invoice->paid_at === null) {
            $this->invoiceHasPayment = false;
            $this->invoicePaymentForm = [];

            return;
        }

        $paidMethod = $invoice->paid_payment_method ?? $invoice->payment_method;

        $this->invoiceHasPayment = true;
        $this->invoicePaymentForm = [
            'paid_at' => $invoice->paid_at->toDateString(),
            'payment_method' => $paidMethod->value,
            'amount' => $this->formatDecimalForInput($invoice->paid_amount ?? $invoice->total),
        ];
    }

    protected function applyInvoicePaymentForm(Invoice $invoice): void
    {
        if (! $this->invoiceHasPayment) {
            return;
        }

        $invoice->paid_at = Carbon::parse($this->invoicePaymentForm['paid_at'])->startOfDay();
        $invoice->paid_payment_method = PaymentMethod::from($this->invoicePaymentForm['payment_method']);
        $invoice->paid_amount = round($this->parseDecimal($this->invoicePaymentForm['amount']), 2);
        $invoice->refreshStatus();
    }

    protected function recalculateItemTotal(int $index): void
    {
        if (! isset($this->invoiceItems[$index])) {
            return;
        }

        $qty = $this->parseDecimal($this->invoiceItems[$index]['quantity'] ?? null);
        $price = $this->parseDecimal($this->invoiceItems[$index]['unit_price'] ?? null);
        $this->invoiceItems[$index]['total'] = (string) round($qty * $price, 2);
    }

    protected function recalculateAllItemTotals(): void
    {
        foreach (array_keys($this->invoiceItems) as $index) {
            $this->recalculateItemTotal($index);
        }
    }

    protected function applyFilterPeriodDates(): void
    {
        $now = now();

        match ($this->filterPeriod) {
            'current_month' => [
                $this->filterDateFrom = $now->copy()->startOfMonth()->toDateString(),
                $this->filterDateTo = $now->copy()->endOfMonth()->toDateString(),
            ],
            'last_month' => [
                $this->filterDateFrom = $now->copy()->subMonth()->startOfMonth()->toDateString(),
                $this->filterDateTo = $now->copy()->subMonth()->endOfMonth()->toDateString(),
            ],
            'current_year' => [
                $this->filterDateFrom = $now->copy()->startOfYear()->toDateString(),
                $this->filterDateTo = $now->copy()->endOfYear()->toDateString(),
            ],
            'last_year' => [
                $this->filterDateFrom = $now->copy()->subYear()->startOfYear()->toDateString(),
                $this->filterDateTo = $now->copy()->subYear()->endOfYear()->toDateString(),
            ],
            'all' => [
                $this->filterDateFrom = null,
                $this->filterDateTo = null,
            ],
            default => null,
        };
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function knownPartners(string $search = ''): array
    {
        $profileId = ActiveCompanyProfile::id();

        if (! $profileId) {
            return [];
        }

        return Invoice::query()
            ->where('company_profile_id', $profileId)
            ->when($search !== '', function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('partner_name', 'like', $term)
                        ->orWhere('partner_ico', 'like', $term);
                });
            })
            ->select('partner_name', 'partner_ico', 'partner_city')
            ->distinct()
            ->orderBy('partner_name')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->partner_name,
                'ico' => $row->partner_ico ?? '',
                'city' => $row->partner_city ?? '',
            ])
            ->all();
    }

    protected function resolvePartnerEmailForInvoice(): ?string
    {
        $profileId = ActiveCompanyProfile::id();

        if (! $profileId) {
            return null;
        }

        $ico = filled($this->invoiceForm['partner_ico'] ?? null) ? $this->invoiceForm['partner_ico'] : null;
        $name = trim((string) ($this->invoiceForm['partner_name'] ?? ''));

        $query = InvoiceEmailLog::query()->where('company_profile_id', $profileId);

        if ($ico) {
            $query->where('partner_ico', $ico);
        } elseif ($name !== '') {
            $query->where('partner_name', $name);
        } else {
            return null;
        }

        return $query->latest('sent_at')->value('to_email');
    }

    protected function buildDefaultInvoiceEmailBody(?string $companyName, ?string $locale = null): string
    {
        $companyName = trim((string) $companyName);
        $locale = $this->normalizeInvoiceEmailLocale($locale);
        $signature = $companyName !== ''
            ? $companyName
            : Lang::get('app.invoices.email.default_signature', [], $locale);

        return Lang::get('app.invoices.email.default_body', ['signature' => $signature], $locale);
    }

    protected function defaultInvoiceEmailLocale(): string
    {
        $locale = app()->getLocale();

        return array_key_exists($locale, config('locales.available', []))
            ? $locale
            : (string) config('app.locale', 'sk');
    }

    protected function normalizeInvoiceEmailLocale(?string $locale = null): string
    {
        $locale = $locale ?? $this->invoiceEmailForm['locale'] ?? $this->defaultInvoiceEmailLocale();

        return array_key_exists($locale, config('locales.available', []))
            ? $locale
            : $this->defaultInvoiceEmailLocale();
    }

    protected function resolveInvoiceEmailLocale(): string
    {
        return $this->normalizeInvoiceEmailLocale($this->invoiceEmailForm['locale'] ?? null);
    }

    protected function refreshInvoiceEmailDefaults(string $locale): void
    {
        $locale = $this->normalizeInvoiceEmailLocale($locale);
        $profile = ActiveCompanyProfile::get();
        $number = trim((string) ($this->invoiceForm['number'] ?? ''));
        $total = $this->invoiceGrandTotal();
        $currency = $this->invoiceForm['currency'] ?? 'EUR';

        $this->invoiceEmailForm['locale'] = $locale;
        $this->invoiceEmailForm['subject'] = Lang::get('app.invoices.email.subject_template', [
            'number' => $number,
            'amount' => $this->formatInvoiceMoney($total, $currency),
        ], $locale);
        $this->invoiceEmailForm['body'] = $this->buildDefaultInvoiceEmailBody($profile?->name, $locale);
    }

    protected function formatInvoiceMoney(float $amount, string $currency = 'EUR'): string
    {
        return number_format($amount, 2, ',', ' ').' '.$currency;
    }

    protected function ensureInvoiceSavedForExport(): bool
    {
        if ($this->selectedInvoiceId) {
            $invoice = $this->invoiceQuery()->find($this->selectedInvoiceId);

            if ($invoice?->isLocked()) {
                return true;
            }
        }

        $this->recalculateAllItemTotals();
        $this->saveInvoice();

        if (! $this->selectedInvoiceId) {
            session()->flash('status', __('app.validation.invoice.export_check_save'));
            $this->dispatch('scroll-to-first-error');

            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $preview
     * @return array<string, mixed>
     */
    protected function buildInvoiceEmailPaymentDetails(array $preview, ?string $locale = null): array
    {
        $locale = $this->normalizeInvoiceEmailLocale($locale);
        $paymentMethodValue = $preview['payment_method_value'] ?? PaymentMethod::BankTransfer->value;
        $paymentMethod = PaymentMethod::tryFrom($paymentMethodValue) ?? PaymentMethod::BankTransfer;

        return [
            'number' => $preview['number'] ?? '',
            'total_formatted' => $this->formatInvoiceMoney(
                (float) ($preview['total'] ?? 0),
                (string) ($preview['currency'] ?? 'EUR'),
            ),
            'payment_method' => Lang::get('app.enums.payment_method.'.$paymentMethod->value, [], $locale),
            'is_bank_transfer' => (bool) ($preview['is_bank_transfer'] ?? false),
            'iban' => filled($preview['iban'] ?? null) ? preg_replace('/\s+/', '', $preview['iban']) : null,
            'swift' => $preview['swift'] ?? null,
            'due_date' => $preview['due_date'] ?? null,
            'pay_by_square_qr_url' => $preview['pay_by_square_qr_url'] ?? null,
        ];
    }

    protected function persistInvoiceEmailMetadata(\Illuminate\Support\Carbon $sentAt): ?Invoice
    {
        if (! $this->selectedInvoiceId) {
            return null;
        }

        $invoice = $this->invoiceQuery()->find($this->selectedInvoiceId);

        if (! $invoice) {
            return null;
        }

        $invoice->forceFill(['emailed_at' => $sentAt])->save();

        return $invoice;
    }
}
