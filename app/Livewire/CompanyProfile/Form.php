<?php

namespace App\Livewire\CompanyProfile;

use App\Models\CompanyProfile;
use App\Services\SubjektApiService;
use App\Support\ActiveCompanyProfile;
use App\Support\GoogleDriveBackupDispatcher;
use App\Support\CompanyProfiles;
use App\TaxpayerType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $companyProfileId = null;

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
    protected array $originalState = [];

    public function mount(?int $companyProfileId = null): void
    {
        $this->companyProfileId = $companyProfileId;

        if ($companyProfileId) {
            $companyProfile = CompanyProfiles::query()->findOrFail($companyProfileId);

            $this->fill($companyProfile->toFormArray());
            $this->existingLogoUrl = $companyProfile->logoUrl();
            $this->existingStampUrl = $companyProfile->stampUrl();
            $this->originalState = $this->currentState();
        }
    }

    public function isEditing(): bool
    {
        return $this->companyProfileId !== null;
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

    public function isDirty(): bool
    {
        if (! $this->isEditing()) {
            return true;
        }

        if ($this->logo !== null || $this->stamp !== null) {
            return true;
        }

        if ($this->removeExistingLogo || $this->removeExistingStamp) {
            return true;
        }

        return $this->currentState() !== $this->originalState;
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());

        $data = collect($validated)->except(['logo', 'stamp'])->all();
        $data['taxpayer_type'] = TaxpayerType::from($data['taxpayer_type']);

        if ($this->isEditing()) {
            $profile = CompanyProfiles::query()->findOrFail($this->companyProfileId);
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

        if ($this->isEditing()) {
            $profile->refresh();
            $this->existingLogoUrl = $profile->logoUrl();
            $this->existingStampUrl = $profile->stampUrl();
            $this->logo = null;
            $this->stamp = null;
            $this->removeExistingLogo = false;
            $this->removeExistingStamp = false;
            $this->originalState = $this->currentState();
            session()->flash('status', __('app.messages.company_profile_saved'));
            $this->dispatch('company-profile-saved');

            GoogleDriveBackupDispatcher::dispatch();

            return;
        }

        $this->dispatch('company-profile-created');

        GoogleDriveBackupDispatcher::dispatch();
    }

    public function delete(): void
    {
        if (! $this->isEditing() || ! $this->confirmDelete) {
            return;
        }

        $profile = CompanyProfiles::query()->findOrFail($this->companyProfileId);
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

        $this->dispatch('company-profile-deleted');

        GoogleDriveBackupDispatcher::dispatch();
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
    protected function rules(): array
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
    protected function messages(): array
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
    protected function currentState(): array
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

    public function render()
    {
        return view('livewire.company-profile.form', [
            'taxpayerTypes' => TaxpayerType::cases(),
        ]);
    }
}
