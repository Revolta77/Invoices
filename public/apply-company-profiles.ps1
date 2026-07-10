# Spusti v PowerShell ako Administrator:
#   Set-ExecutionPolicy Bypass -Scope Process -Force
#   & "C:\ProgramData\envkit\projects\faktury\public\apply-company-profiles.ps1"

$project = "C:\ProgramData\envkit\projects\faktury"
$paths = @(
    "$project\app",
    "$project\routes",
    "$project\bootstrap",
    "$project\config"
)

foreach ($p in $paths) {
    takeown /F $p /R /D Y 2>$null | Out-Null
    icacls $p /grant "${env:USERNAME}:(OI)(CI)F" /T 2>$null | Out-Null
}

$dashboardPhp = @'
<?php

namespace App\Livewire;

use App\Livewire\Concerns\ManagesCompanyProfileForm;
use App\Support\ActiveCompanyProfile;
use App\Support\CompanyProfiles;
use App\TaxpayerType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Faktury')]
class Dashboard extends Component
{
    use ManagesCompanyProfileForm;

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
    }

    public function updatedView(): void
    {
        $this->loadCompanyProfileForm();
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
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
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
        ]);
    }
}
'@

$webRoutes = @'
<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\GoogleLinkController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/auth/google/link', [GoogleLinkController::class, 'redirect'])
        ->name('auth.google.link');
    Route::get('/auth/google/link/callback', [GoogleLinkController::class, 'linkCallback'])
        ->name('auth.google.link.callback');

    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
});
'@

$bootstrapApp = @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'company.profile' => \App\Http\Middleware\EnsureCompanyProfile::class,
        ]);

        $middleware->redirectGuestsTo('/');
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
'@

Set-Content -Path "$project\app\Livewire\Dashboard.php" -Value $dashboardPhp -Encoding UTF8
Set-Content -Path "$project\routes\web.php" -Value $webRoutes -Encoding UTF8
Set-Content -Path "$project\bootstrap\app.php" -Value $bootstrapApp -Encoding UTF8

Write-Host "Hotovo: Dashboard.php, routes/web.php a bootstrap/app.php boli aktualizovane."
Write-Host "Spustite: php artisan view:clear"
