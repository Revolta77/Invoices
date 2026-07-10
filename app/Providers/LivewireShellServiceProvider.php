<?php

namespace App\Providers;

use App\Http\Middleware\RedirectDashboardToApp;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LivewireShellServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::pushMiddlewareToGroup('web', RedirectDashboardToApp::class);

        $this->loadRoutesFrom(base_path('routes/app-shell.php'));
    }
}
