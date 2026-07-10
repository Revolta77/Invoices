<?php

namespace App\Livewire;

use App\Models\CompanyProfile;
use App\InvoiceStatus;
use App\Livewire\Concerns\ManagesCompanyProfileForm;
use App\Livewire\Concerns\ManagesInvoices;
use App\PaymentMethod;
use App\Support\ActiveCompanyProfile;
use App\Support\CompanyProfiles;
use App\TaxpayerType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class AppShell extends Component
{
    use ManagesCompanyProfileForm;
    use ManagesInvoices;

    #[Url(as: 'view', history: true, keep: true)]
    public string $view = 'home';

    #[Url(as: 'profile', history: true, keep: true)]
    public ?int $profile = null;

    public function mount(): void
    {
        if (! CompanyProfiles::exists()) {
            $this->view = 'company-create';
            $this->profile = null;
            $this->loadCompanyProfileForm();

            return;
        }

        ActiveCompanyProfile::ensureSelected(auth()->user());

        if ($this->view === 'company-create' && CompanyProfiles::count() > 0) {
            $this->view = 'home';
        }

        if ($this->view === 'company-edit' && ! $this->profile) {
            $active = ActiveCompanyProfile::get();
            $this->profile = $active?->id;
        }

        if ($this->view === 'home' && ! ActiveCompanyProfile::get()) {
            $this->view = 'company-create';
        }

        $this->loadCompanyProfileForm();

        if ($this->view === 'home' && ActiveCompanyProfile::get()) {
            $this->initInvoiceDashboard();
        }
    }

    public function updatedView(): void
    {
        $this->loadCompanyProfileForm();

        if ($this->view === 'home' && ActiveCompanyProfile::get()) {
            $this->initInvoiceDashboard();
        }
    }

    public function updatedProfile(): void
    {
        if ($this->view === 'company-edit') {
            $this->loadCompanyProfileForm();
        }
    }

    public function switchProfile(int $profileId): void
    {
        $companyProfile = CompanyProfiles::query()->findOrFail($profileId);
        ActiveCompanyProfile::set($companyProfile->id);
        $this->view = 'home';
        $this->profile = null;
        $this->closeInvoicePanel();
        $this->initInvoiceDashboard();
    }

    public function goToCreateProfile(): void
    {
        $this->view = 'company-create';
        $this->profile = null;
        $this->loadCompanyProfileForm();
    }

    public function goToEditProfile(?int $profileId = null): void
    {
        $profileId ??= ActiveCompanyProfile::id();

        if (! $profileId) {
            return;
        }

        $this->profile = $profileId;
        $this->view = 'company-edit';
        $this->loadCompanyProfileForm();
    }

    public function goToSettings(): void
    {
        $this->view = 'settings';
        $this->profile = null;
    }

    public function goHome(): void
    {
        if (! CompanyProfiles::exists()) {
            $this->view = 'company-create';
            $this->loadCompanyProfileForm();

            return;
        }

        ActiveCompanyProfile::ensureSelected(auth()->user());
        $this->view = 'home';
        $this->profile = null;
        $this->initInvoiceDashboard();
    }

    public function logout(): void
    {
        auth()->guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.app-shell', [
            'activeProfile' => ActiveCompanyProfile::get(),
            'profiles' => CompanyProfiles::query()->orderBy('name')->get(),
            'taxpayerTypes' => TaxpayerType::cases(),
            'invoiceStatuses' => InvoiceStatus::cases(),
            'paymentMethods' => PaymentMethod::cases(),
        ])->title(config('app.name'));
    }
}
