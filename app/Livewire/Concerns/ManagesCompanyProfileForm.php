<?php

namespace App\Livewire\Concerns;

use App\Models\CompanyProfile;
use App\Services\SubjektApiService;
use App\Support\ActiveCompanyProfile;
use App\Support\GoogleDriveBackupDispatcher;
use App\Support\CompanyProfiles;
use App\TaxpayerType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

trait ManagesCompanyProfileForm
{
    use WithFileUploads;

    public string $name = '';

    public string $street = '';

    public string $postal_code = '';

    public string $city = '';

    public string $country = 'SK';

    public string $ico = '';

    public string $dic = '';

    public string $taxpayer_type = 'neplatitel_dph';

    public string $ic_dph = '';

    public string $registry = '';

    public string $email = '';

    public string $phone = '';

    public string $web = '';

    public $logo = null;

    public $stamp = null;

    public ?string $existingLogoUrl = null;

    public ?string $existingStampUrl = null;

    public bool $removeExistingLogo = false;

    public bool $removeExistingStamp = false;

    /** @var array<int, array<string, mixed>> */
    public array $searchResults = [];

    public bool $showSearchResults = false;

    public bool $confirmDelete = false;

    /** @var array<string, string> */
    protected array $formOriginalState = [];

    protected function loadCompanyProfileForm(): void
    {
        if ($this->view === 'company-edit' && $this->profile) {
            $companyProfile = CompanyProfiles::query()->findOrFail($this->profile);

            $this->fill($companyProfile->toFormArray());
            $this->existingLogoUrl = $companyProfile->logoUrl();
            $this->existingStampUrl = $companyProfile->stampUrl();
            $this->formOriginalState = $this->formCurrentState();
            $this->confirmDelete = false;

            return;
        }

        if ($this->view === 'company-create') {
            $this->resetCompanyProfileForm();
        }
    }

    protected function resetCompanyProfileForm(): void
    {
        $this->reset([
            'name', 'street', 'postal_code', 'city', 'country', 'ico', 'dic',
            'taxpayer_type', 'ic_dph', 'registry', 'email', 'phone', 'web',
            'logo', 'stamp', 'existingLogoUrl', 'existingStampUrl',
            'removeExistingLogo', 'removeExistingStamp', 'searchResults',
            'showSearchResults', 'confirmDelete',
        ]);

        $this->country = 'SK';
        $this->taxpayer_type = TaxpayerType::NeplatitelDph->value;
        $this->formOriginalState = [];
    }

    public function isEditingProfile(): bool
    {
        return $this->view === 'company-edit' && $this->profile !== null;
    }

    public function updatedName(): void
    {
        $query = trim($this->name);

        if (mb_strlen($query) < 2) {
            $this->searchResults = [];
            $this->showSearchResults = false;

            return;
        }

        $this->searchResults = app(SubjektApiService::class)->search($query);
        $this->showSearchResults = count($this->searchResults) > 0;
    }

    public function selectCompany(string $ico): void
    {
        $entity = app(SubjektApiService::class)->entity($ico);

        if (! $entity) {
            return;
        }

        $mapped = app(SubjektApiService::class)->mapToFormFields($entity);
        $this->fill($mapped);
        $this->showSearchResults = false;
        $this->searchResults = [];
    }

    public function hideSearchResults(): void
    {
        $this->showSearchResults = false;
    }

    public function isProfileFormDirty(): bool
    {
        if (! $this->isEditingProfile()) {
            return true;
        }

        if ($this->logo !== null || $this->stamp !== null) {
            return true;
        }

        if ($this->removeExistingLogo || $this->removeExistingStamp) {
            return true;
        }

        return $this->formCurrentState() !== $this->formOriginalState;
    }

    public function saveProfile(): void
    {
        $validated = $this->validateProfileForm();

        if ($validated === null) {
            return;
        }

        $data = collect($validated)->except(['logo', 'stamp'])->all();
        $data['taxpayer_type'] = TaxpayerType::from($data['taxpayer_type']);

        if ($this->isEditingProfile()) {
            $profile = CompanyProfiles::query()->findOrFail($this->profile);
            $profile->update($data);
        } else {
            $profile = CompanyProfiles::query()->create(array_merge($data, [
                'user_id' => auth()->id(),
            ]));
            ActiveCompanyProfile::set($profile->id);
        }

        if ($this->removeExistingLogo && $this->logo === null) {
            $this->deleteStoredImage($profile, 'logo_path');
        }

        if ($this->removeExistingStamp && $this->stamp === null) {
            $this->deleteStoredImage($profile, 'stamp_path');
        }

        $this->storeUpload($profile, 'logo', 'logo_path');
        $this->storeUpload($profile, 'stamp', 'stamp_path');

        if ($this->isEditingProfile()) {
            $profile->refresh();
            $this->existingLogoUrl = $profile->logoUrl();
            $this->existingStampUrl = $profile->stampUrl();
            $this->logo = null;
            $this->stamp = null;
            $this->removeExistingLogo = false;
            $this->removeExistingStamp = false;
            $this->formOriginalState = $this->formCurrentState();
            session()->flash('status', __('app.messages.company_profile_saved'));

            GoogleDriveBackupDispatcher::dispatch();

            return;
        }

        $this->goHome();

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function deleteProfile(): void
    {
        if (! $this->isEditingProfile() || ! $this->confirmDelete) {
            return;
        }

        $profile = CompanyProfiles::query()->findOrFail($this->profile);
        $profileId = $profile->id;
        $wasActive = ActiveCompanyProfile::id() === $profileId;

        if ($profile->logo_path) {
            Storage::disk('public')->delete($profile->logo_path);
        }

        if ($profile->stamp_path) {
            Storage::disk('public')->delete($profile->stamp_path);
        }

        $profile->delete();

        if ($wasActive) {
            ActiveCompanyProfile::clear();
        }

        GoogleDriveBackupDispatcher::dispatch();

        $this->goHome();
    }

    public function removeLogo(): void
    {
        $this->logo = null;

        if ($this->existingLogoUrl) {
            $this->removeExistingLogo = true;
            $this->existingLogoUrl = null;
        }
    }

    public function removeStamp(): void
    {
        $this->stamp = null;

        if ($this->existingStampUrl) {
            $this->removeExistingStamp = true;
            $this->existingStampUrl = null;
        }
    }

    public function updatedLogo(): void
    {
        $this->removeExistingLogo = false;
    }

    public function updatedStamp(): void
    {
        $this->removeExistingStamp = false;
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>|null
     */
    protected function validateProfileForm(): ?array
    {
        $rules = $this->profileFormRules();
        $data = collect(array_keys($rules))
            ->mapWithKeys(fn (string $key) => [$key => $this->{$key}])
            ->all();

        $validator = Validator::make($data, $rules, $this->profileFormMessages());

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            $this->dispatch('scroll-to-first-error');

            return null;
        }

        return $validator->validated();
    }

    protected function profileFormRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2'],
            'ico' => ['nullable', 'string', 'max:16'],
            'dic' => ['nullable', 'string', 'max:16'],
            'taxpayer_type' => ['required', Rule::enum(TaxpayerType::class)],
            'ic_dph' => ['nullable', 'string', 'max:20'],
            'registry' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'web' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'stamp' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function profileFormMessages(): array
    {
        return [
            'name.required' => __('app.validation.company.name_required'),
            'country.required' => __('app.validation.company.country_required'),
            'email.email' => __('app.validation.company.email_invalid'),
            'web.url' => __('app.validation.company.web_invalid'),
            'logo.mimes' => __('app.validation.company.logo_mimes'),
            'stamp.mimes' => __('app.validation.company.stamp_mimes'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function formCurrentState(): array
    {
        return [
            'name' => $this->name,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'country' => $this->country,
            'ico' => $this->ico,
            'dic' => $this->dic,
            'taxpayer_type' => $this->taxpayer_type,
            'ic_dph' => $this->ic_dph,
            'registry' => $this->registry,
            'email' => $this->email,
            'phone' => $this->phone,
            'web' => $this->web,
        ];
    }

    protected function storeUpload(CompanyProfile $profile, string $property, string $column): void
    {
        if ($this->{$property} === null) {
            return;
        }

        if ($profile->{$column}) {
            Storage::disk('public')->delete($profile->{$column});
        }

        $path = $this->{$property}->store('company-profiles/'.$profile->id, 'public');
        $profile->update([$column => $path]);
    }

    protected function deleteStoredImage(CompanyProfile $profile, string $column): void
    {
        if (! $profile->{$column}) {
            return;
        }

        Storage::disk('public')->delete($profile->{$column});
        $profile->update([$column => null]);
    }
}
